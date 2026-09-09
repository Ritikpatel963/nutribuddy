<?php

namespace App\Http\Controllers\Api\Nutrition;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nutrition\ChildAssessment;
use App\Models\Nutrition\Child;
use App\Services\Nutrition\AssessmentEngine;
use App\Services\Nutrition\DietChartGeneratorService;
use App\Models\Nutrition\QuestionnaireAnswer;

class QuestionnaireController extends Controller
{
    protected $engine;
    protected $dietChartGenerator;

    public function __construct(AssessmentEngine $engine, DietChartGeneratorService $dietChartGenerator)
    {
        $this->engine = $engine;
        $this->dietChartGenerator = $dietChartGenerator;
    }

    /**
     * Get the dynamic questionnaire for a specific child.
     */
    public function getQuestions($childId)
    {
        $child = Child::findOrFail($childId);
        // Assuming one active assessment for now
        $assessment = $child->assessments()->latest()->first();
        
        if (!$assessment) {
            return response()->json(['message' => 'No assessment found'], 404);
        }

        $questions = $this->engine->getQuestionsForChild($child, $assessment);
        
        return response()->json([
            'model_level' => $assessment->model_level,
            'questions' => $questions
        ]);
    }

    /**
     * Submit initial answers and create child.
     */
    public function submitInitialAnswers(Request $request)
    {
        try {
            // $request contains q_1, q_2, q_3 ... etc
            // 1 = Name, 2 = DOB, 3 = Sex
            $existingChild = $request->filled('child_id')
                ? Child::where('id', $request->integer('child_id'))->where('user_id', auth()->id())->firstOrFail()
                : null;
            $name = $request->input('q_1', $existingChild?->name ?? 'Child');

            $dobInput = $request->input('q_2');
            $dob = null;
            if ($dobInput && strpos($dobInput, '-') !== false && strlen($dobInput) > 5 && strtotime($dobInput) !== false) {
                $dob = \Carbon\Carbon::parse($dobInput);
            }

            $sexInput = $request->input('q_3', 'boy');
            $sex = strtolower(is_array($sexInput) ? $sexInput[0] : $sexInput);
            if (!in_array($sex, ['boy', 'girl'])) {
                $sex = 'boy';
            }

            $child = $existingChild ?: Child::create([
                'user_id' => auth()->id() ?? 1, 'name' => $name, 'dob' => $dob, 'sex' => $sex,
            ]);

            $planSlug = $request->input('plan_slug', 'basic');

            $previousAssessment = $child->assessments()->latest()->first();
            $assessment = ChildAssessment::create([
                'child_id' => $child->id,
                'model_level' => $planSlug,
                'current_weight_kg' => (float)$request->input('q_4', 0) ?: null,
                'current_height_cm' => (float)$request->input('q_5', 0) ?: null,
                'diet_type' => is_array($request->input('q_6')) ? implode(', ', $request->input('q_6')) : ($request->input('q_6') ?? 'vegetarian'),
                'allergies' => is_array($request->input('q_7')) ? implode(', ', $request->input('q_7')) : ($request->input('q_7') ?? 'None'),
                'region' => is_array($request->input('q_8')) ? implode(', ', $request->input('q_8')) : ($request->input('q_8') ?? 'Pan India'),
                'activity_level' => is_array($request->input('q_13')) ? implode(', ', $request->input('q_13')) : $request->input('q_13'),
                'main_goal' => is_array($request->input('q_22')) ? implode(', ', $request->input('q_22')) : $request->input('q_22'),
                'budget' => is_array($request->input('q_35')) ? implode(', ', $request->input('q_35')) : $request->input('q_35'),
                'picky_level' => is_array($request->input('q_16')) ? implode(', ', $request->input('q_16')) : $request->input('q_16'),
                'current_season' => $this->getCurrentIndianSeason(),
            ]);

            // Collect all answers (skipping DB save - questions table not seeded yet)
            $answersArray = [];
            if ($previousAssessment) {
                foreach ($previousAssessment->answers as $answer) {
                    $answersArray[$answer->question_id] = $answer->answer_value;
                    QuestionnaireAnswer::create(['child_assessment_id' => $assessment->id, 'question_id' => $answer->question_id, 'answer_value' => $answer->answer_value]);
                }
            }
            foreach ($request->all() as $key => $value) {
                if (str_starts_with($key, 'q_')) {
                    $questionId = str_replace('q_', '', $key);
                    $answersArray[$questionId] = $value;
                    QuestionnaireAnswer::updateOrCreate(
                        ['child_assessment_id' => $assessment->id, 'question_id' => $questionId],
                        ['answer_value' => $value]
                    );
                }
            }

            return response()->json([
                'success' => true,
                'child_id' => $child->id,
                'assessment_id' => $assessment->id,
                'message' => 'Assessment saved. Complete payment to generate the diet chart for review.'
            ]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('submitInitialAnswers failed: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate the current Indian season based on month
     */
    private function getCurrentIndianSeason(): string
    {
        $month = now()->month;

        return match ($month) {
            3, 4 => 'Vasant (Spring)',
            5, 6 => 'Greeshma (Summer)',
            7, 8, 9 => 'Varsha (Monsoon)',
            10, 11 => 'Sharad (Autumn)',
            12, 1 => 'Hemant (Pre-winter)',
            2 => 'Shishir (Winter)',
            default => 'Year-round',
        };
    }
}
