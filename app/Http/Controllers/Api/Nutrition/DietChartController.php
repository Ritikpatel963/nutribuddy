<?php

namespace App\Http\Controllers\Api\Nutrition;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nutrition\ChildAssessment;
use App\Models\Nutrition\Child;
use App\Models\Nutrition\QuestionnaireAnswer;
use App\Services\Nutrition\DietChartGeneratorService;
use App\Services\Nutrition\AssessmentEngine;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DietChartController extends Controller
{
    protected $dietChartGenerator;
    protected $assessmentEngine;

    public function __construct(
        DietChartGeneratorService $dietChartGenerator,
        AssessmentEngine $assessmentEngine
    ) {
        $this->dietChartGenerator = $dietChartGenerator;
        $this->assessmentEngine = $assessmentEngine;
    }

    /**
     * Store questionnaire answers and generate 7-day diet chart.
     * 
     * Expected request format:
     * {
     *   "child_id": 1,
     *   "assessment_id": 1,
     *   "answers": {
     *     "1": "Child Name",
     *     "2": "2018-05-15",
     *     "3": "boy",
     *     "4": 25.5,
     *     "5": 115,
     *     "6": "vegetarian",
     *     "7": ["nuts", "dairy"],
     *     "8": "North",
     *     ...
     *   }
     * }
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate request
            $validated = $request->validate([
                'child_id' => 'required|integer|exists:children,id',
                'assessment_id' => 'required|integer|exists:child_assessments,id',
                'answers' => 'required|array',
            ]);

            $child = Child::findOrFail($validated['child_id']);
            $assessment = ChildAssessment::findOrFail($validated['assessment_id']);

            // Verify child-assessment relationship
            if ($assessment->child_id !== $child->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Child-assessment mismatch',
                ], 422);
            }

            // Store questionnaire answers
            $this->storeAnswers($assessment, $validated['answers']);

            // Generate 7-day diet chart
            $result = $this->dietChartGenerator->generateDietChart($assessment, $validated['answers']);

            if (!$result['success']) {
                return response()->json($result, 500);
            }

            return response()->json([
                'success' => true,
                'message' => '7-day diet chart generated successfully',
                'data' => $result,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Diet Chart Store Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to generate diet chart',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate or regenerate diet chart for existing assessment.
     * 
     * @param int $assessmentId
     * @return JsonResponse
     */
    public function generate(int $assessmentId): JsonResponse
    {
        try {
            $assessment = ChildAssessment::findOrFail($assessmentId);

            // Fetch existing answers
            $answers = $assessment->answers()
                ->pluck('answer_value', 'question_id')
                ->map(function ($value) {
                    return json_decode($value, true);
                })
                ->toArray();

            // Regenerate diet chart
            $result = $this->dietChartGenerator->generateDietChart($assessment, $answers);

            if (!$result['success']) {
                return response()->json($result, 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Diet chart generated successfully',
                'data' => $result,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Diet Chart Generation Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to generate diet chart',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get generated diet chart details.
     * 
     * @param int $assessmentId
     * @return JsonResponse
     */
    public function show(int $assessmentId): JsonResponse
    {
        try {
            $assessment = ChildAssessment::findOrFail($assessmentId);

            // Fetch latest diet plan
            $dietPlan = $assessment->dietPlan()
                ->with('meals.items.food')
                ->latest()
                ->first();

            if (!$dietPlan) {
                return response()->json([
                    'success' => false,
                    'error' => 'No diet plan found for this assessment',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $this->formatDietPlanResponse($dietPlan, $assessment),
            ], 200);

        } catch (\Exception $e) {
            Log::error('Diet Chart Show Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve diet chart',
            ], 500);
        }
    }

    /**
     * Get nutrient gap analysis for assessment.
     * 
     * Returns detailed adequacy analysis comparing achieved intake vs ICMR targets.
     * 
     * @param int $assessmentId
     * @return JsonResponse
     */
    public function gapAnalysis(int $assessmentId): JsonResponse
    {
        try {
            $assessment = ChildAssessment::findOrFail($assessmentId);
            $child = $assessment->child;

            // Fetch ICMR targets
            $targetEngine = app()->make('App\Services\Nutrition\TargetEngine');
            $icmrTarget = $targetEngine->getTargets($child);

            // Fetch questionnaire answers
            $answers = $assessment->answers()
                ->pluck('answer_value', 'question_id')
                ->map(function ($value) {
                    return json_decode($value, true);
                })
                ->toArray();

            // Calculate gaps
            $gaps = $this->dietChartGenerator->calculateGaps($assessment, $answers, $icmrTarget);

            return response()->json([
                'success' => true,
                'data' => [
                    'child_id' => $child->id,
                    'child_name' => $child->name,
                    'age_years' => $child->dob ? $child->dob->diffInYears(now()) : null,
                    'sex' => $child->sex,
                    'icmr_targets' => [
                        'age_range' => $icmrTarget->age_min . '-' . $icmrTarget->age_max . ' years',
                        'energy_kcal' => $icmrTarget->energy_kcal,
                        'protein_g' => $icmrTarget->protein_g,
                        'calcium_mg' => $icmrTarget->calcium_mg,
                        'iron_mg' => $icmrTarget->iron_mg,
                        'zinc_mg' => $icmrTarget->zinc_mg,
                    ],
                    'nutrient_gaps' => $gaps,
                    'gap_summary' => [
                        'critical_gaps' => collect($gaps)->where('gap_category', 'Critical gap')->count(),
                        'significant_gaps' => collect($gaps)->where('gap_category', 'Significant gap')->count(),
                        'partial_gaps' => collect($gaps)->where('gap_category', 'Partial gap')->count(),
                        'met_nutrients' => collect($gaps)->where('gap_category', 'Met')->count(),
                    ],
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Gap Analysis Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to analyze nutrient gaps',
            ], 500);
        }
    }

    /**
     * List all diet plans for a child.
     * 
     * @param int $childId
     * @return JsonResponse
     */
    public function listByChild(int $childId): JsonResponse
    {
        try {
            $child = Child::findOrFail($childId);

            $assessments = $child->assessments()
                ->with('dietPlan')
                ->get()
                ->map(function ($assessment) {
                    return [
                        'assessment_id' => $assessment->id,
                        'model_level' => $assessment->model_level,
                        'created_at' => $assessment->created_at,
                        'latest_diet_plan' => $assessment->dietPlan ? [
                            'id' => $assessment->dietPlan->id,
                            'version' => $assessment->dietPlan->version,
                            'status' => $assessment->dietPlan->status,
                            'created_at' => $assessment->dietPlan->created_at,
                        ] : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'child_id' => $child->id,
                'child_name' => $child->name,
                'assessments' => $assessments,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to list diet plans',
            ], 500);
        }
    }

    /**
     * Update assessment and regenerate diet chart.
     * 
     * @param Request $request
     * @param int $assessmentId
     * @return JsonResponse
     */
    public function update(Request $request, int $assessmentId): JsonResponse
    {
        try {
            $assessment = ChildAssessment::findOrFail($assessmentId);

            // Update assessment data
            $updateData = $request->validate([
                'current_weight_kg' => 'nullable|numeric|min:0',
                'current_height_cm' => 'nullable|numeric|min:0',
                'main_goal' => 'nullable|string',
                'diet_type' => 'nullable|string',
                'allergies' => 'nullable|array',
                'region' => 'nullable|string',
                'budget' => 'nullable|string',
            ]);

            $assessment->update($updateData);

            // Regenerate diet chart
            $answers = $assessment->answers()
                ->pluck('answer_value', 'question_id')
                ->map(function ($value) {
                    return json_decode($value, true);
                })
                ->toArray();

            $result = $this->dietChartGenerator->generateDietChart($assessment, $answers);

            if (!$result['success']) {
                return response()->json($result, 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Assessment updated and diet chart regenerated',
                'data' => $result,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Assessment Update Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to update assessment',
            ], 500);
        }
    }

    /**
     * Store questionnaire answers to database.
     * 
     * @param ChildAssessment $assessment
     * @param array $answers
     * @return void
     */
    protected function storeAnswers(ChildAssessment $assessment, array $answers): void
    {
        foreach ($answers as $questionId => $answerValue) {
            // Update or create answer
            QuestionnaireAnswer::updateOrCreate(
                [
                    'child_assessment_id' => $assessment->id,
                    'question_id' => $questionId,
                ],
                [
                    'answer_value' => is_array($answerValue) 
                        ? json_encode($answerValue) 
                        : json_encode((string) $answerValue),
                ]
            );
        }
    }

    /**
     * Format diet plan for API response.
     * 
     * @param mixed $dietPlan
     * @param ChildAssessment $assessment
     * @return array
     */
    protected function formatDietPlanResponse($dietPlan, ChildAssessment $assessment): array
    {
        $planData = json_decode($dietPlan->plan_data ?? '{}', true);

        // Organize meals by day
        $mealsByDay = [];
        foreach ($dietPlan->meals as $meal) {
            $day = $meal->meal_slot; // Assuming meal_slot stores day name
            if (!isset($mealsByDay[$day])) {
                $mealsByDay[$day] = [];
            }

            $mealsByDay[$day][] = [
                'slot' => $meal->meal_slot,
                'timing' => $meal->timing,
                'focus' => $meal->focus,
                'items' => $meal->items->map(function ($item) {
                    return [
                        'food_id' => $item->food_id,
                        'food_name' => $item->food->food_name ?? 'N/A',
                        'portion_size' => $item->portion_size,
                    ];
                })->toArray(),
            ];
        }

        return [
            'diet_plan_id' => $dietPlan->id,
            'version' => $dietPlan->version,
            'status' => $dietPlan->status,
            'child_assessment_id' => $assessment->id,
            'ai_summary' => $dietPlan->ai_summary,
            'daily_nutrients' => $planData['daily_nutrients'] ?? [],
            'nutrient_gaps' => $planData['nutrient_gaps'] ?? [],
            'meals_by_day' => $mealsByDay,
            'created_at' => $dietPlan->created_at,
        ];
    }
}
