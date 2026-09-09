<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nutrition\Child;

class ChildProfileController extends Controller
{
    // Removed old show method

    public function userPanelShow(Request $request)
    {
        $child = auth()->user()->children()->latest()->first();

        // If no child exists, redirect to create one
        if (!$child) {
            return redirect()->route('child-profile.create');
        }

        $dietPlan = null;
        $subscription = null;
        $assessment = null;
        if ($child) {
            $assessment = $child->assessments()->latest()->first();
            if ($assessment) {
                $dietPlan = \App\Models\Nutrition\DietPlan::with('meals.items.food')->where('child_assessment_id', $assessment->id)->latest()->first();
            }
            $subscription = \App\Models\DietSubscription::with('subscriptionPlan')
                ->where('child_id', $child->id)->where('payment_status', 'paid')->latest()->first();
        }

        return view('pages.user-panel.child-profile', compact('child', 'dietPlan', 'assessment', 'subscription'));
    }

    /**
     * Show the create/onboarding form
     */
    public function create()
    {
        // If user already has children, show them the profile page instead
        if (auth()->user()->children()->exists()) {
            return redirect()->route('child-profile');
        }

        return view('pages.user-panel.child-profile-create');
    }

    public function store(Request $request)
    {
        // Log incoming request to verify data is received
        \Illuminate\Support\Facades\Log::info('ChildProfileController::store() called', [
            'request_data' => $request->all(),
            'user_id' => auth()->id(),
        ]);

        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'age' => 'required|integer',
                'sex' => 'required|string|in:boy,girl,other',
                'height' => 'required|numeric|min:50|max:220',
                'weight' => 'required|numeric|min:5|max:150',
                'region' => 'required|string',
                'diet' => 'required|string',
                'picky' => 'required|string',
                'allergies' => 'nullable|string',
                'activity' => 'nullable|string',
            ]);

            \Illuminate\Support\Facades\Log::info('Validation passed', ['validated' => $validated]);

            $dob = now()->subYears($validated['age']);

            // Create child
            $child = \App\Models\Nutrition\Child::create([
                'user_id' => auth()->id(),
                'name' => $validated['name'],
                'dob' => $dob,
                'sex' => $validated['sex'] === 'other' ? 'boy' : $validated['sex'], // children table only accepts 'boy' or 'girl'
            ]);

            \Illuminate\Support\Facades\Log::info('Child created', [
                'child_id' => $child->id,
                'name' => $child->name,
                'user_id' => $child->user_id,
                'child_data' => $child->toArray(),
            ]);

            // Create assessment with explicitly cast values
            $assessmentData = [
                'child_id' => (int)$child->id,
                'model_level' => 'champion',
                'current_weight_kg' => (float)$validated['weight'],
                'current_height_cm' => (float)$validated['height'],
                'diet_type' => (string)$validated['diet'],
                'picky_level' => (string)$validated['picky'],
                'activity_level' => (string)$validated['activity'] ?? 'Moderately active',
                'allergies' => $validated['allergies'] ? (string)$validated['allergies'] : null,
                'region' => (string)$validated['region'],
                'current_season' => $this->getCurrentIndianSeason(),
            ];

            \Illuminate\Support\Facades\Log::info('Creating assessment with data', $assessmentData);

            $assessment = \App\Models\Nutrition\ChildAssessment::create($assessmentData);

            \Illuminate\Support\Facades\Log::info('ChildAssessment created', [
                'assessment_id' => $assessment->id,
                'assessment_data' => $assessment->toArray(),
            ]);

            // Refresh from database to verify it was saved
            $assessment->refresh();
            \Illuminate\Support\Facades\Log::info('ChildAssessment after refresh', $assessment->toArray());

            // Optional: Generate diet chart from answers
            try {
                $generator = app(\App\Services\Nutrition\DietChartGeneratorService::class);
                $answers = [
                    1 => $validated['age'],
                    2 => $validated['sex'],
                    3 => $validated['weight'],
                    4 => $validated['height'],
                    5 => $validated['diet'],
                    6 => $validated['allergies'] ?? '',
                    7 => $validated['region'],
                    8 => $validated['picky'],
                ];
                $generator->generateDietChart($assessment, $answers);
                \Illuminate\Support\Facades\Log::info('Diet chart generated successfully', ['assessment_id' => $assessment->id]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Diet chart generation failed during child profile creation', [
                    'child_id' => $child->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Child profile created successfully!',
                'child_id' => $child->id,
                'assessment_id' => $assessment->id,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Validation failed', [
                'errors' => $e->errors(),
                'data' => $request->all(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Unexpected error in store()', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error creating profile: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate the current Indian season based on month
     * Seasons:
     * - Vasant (Spring): March-April
     * - Greeshma (Summer): May-June
     * - Varsha (Monsoon): July-September
     * - Sharad (Autumn): October-November
     * - Hemant (Pre-winter): December-January
     * - Shishir (Winter): February
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
