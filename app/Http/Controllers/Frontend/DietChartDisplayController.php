<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nutrition\Child;
use App\Models\Nutrition\ChildAssessment;
use App\Models\Nutrition\DietPlan;
use App\Models\Nutrition\DietPlanMeal;
use App\Models\DietSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DietChartDisplayController extends Controller
{
    /**
     * Create a new controller instance.
     * Set up route model binding for Child model.
     */
    public function __construct()
    {
        // Route model binding happens at RouteServiceProvider level
        // This is configured in bootstrap/app.php or RouteServiceProvider
    }

    /**
     * Display the diet chart for a specific child.
     * Shows plan if purchased/generated, or purchase prompt if not.
     * 
     * @param Child $child
     * @return \Illuminate\View\View
     */
    public function show(Child $child)
    {
        try {
            // Authorization: Only child's parent can view
            $this->authorizeChildAccess($child);

            // Get latest assessment
            $assessment = $child->assessments()->latest()->first();

            if (!$assessment) {
                return view('child-profile.no-assessment', [
                    'child' => $child,
                    'message' => 'No health assessment found. Please complete the questionnaire first.'
                ]);
            }

            // Get active diet plan
            $dietPlan = $assessment->dietPlans()->where('status', 'active')->where('review_status', 'approved')->latest()->first();

            // Check if user has purchased plans
            $purchasedPlans = $this->getUserPurchasedPlansCount();

            // If no active plan, show purchase prompt
            if (!$dietPlan) {
                return view('child-profile.diet-chart-display', [
                    'child' => $child,
                    'assessment' => $assessment,
                    'hasPlan' => false,
                    'purchasedPlans' => $purchasedPlans,
                    'sevenDayPlan' => [],
                    'nutrientGaps' => [],
                    'clinicalTips' => []
                ]);
            }

            // Parse diet plan data from database
            $planData = json_decode($dietPlan->plan_data, true) ?? [];
            $sevenDayPlan = $this->formatSevenDayPlan($dietPlan, $assessment);
            $nutrientGaps = $planData['nutrient_gaps'] ?? [];
            $clinicalTips = $this->getClinicalTips($assessment, $nutrientGaps);

            return view('child-profile.diet-chart-display', [
                'child' => $child,
                'assessment' => $assessment,
                'dietPlan' => $dietPlan,
                'hasPlan' => true,
                'purchasedPlans' => $purchasedPlans,
                'sevenDayPlan' => $sevenDayPlan,
                'nutrientGaps' => $nutrientGaps,
                'clinicalTips' => $clinicalTips,
                'dailyNutrients' => $planData['daily_nutrients'] ?? []
            ]);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::warning('Unauthorized diet chart access attempt', [
                'user_id' => Auth::id(),
                'child_id' => $child->id ?? null,
                'child_user_id' => $child->user_id ?? null,
            ]);
            return redirect()->route('child-profile')->with('error', 'Unauthorized access. This child profile does not belong to you.');
        } catch (\Exception $e) {
            Log::error('Diet Chart Display Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'child_id' => $child->id ?? null,
                'exception' => get_class($e),
            ]);
            return redirect()->route('child-profile')->with('error', 'Error loading diet plan');
        }
    }

    /**
     * Regenerate diet chart for existing assessment.
     * 
     * @param Child $child
     * @return \Illuminate\Http\RedirectResponse
     */
    public function regenerate(Child $child)
    {
        try {
            $this->authorizeChildAccess($child);

            $assessment = $child->assessments()->latest()->first();

            if (!$assessment) {
                return redirect()->route('child-profile')->with('error', 'No assessment found');
            }

            // Check if user has purchased plans
            $purchasedPlans = $this->getUserPurchasedPlansCount();

            if ($purchasedPlans <= 0) {
                return redirect()->route('child-profile')
                    ->with('error', 'No plans remaining. Purchase more plans to regenerate.');
            }

            // Call diet chart generation service
            $dietChartService = app('App\Services\Nutrition\DietChartGeneratorService');

            // Fetch questionnaire answers
            $answers = $assessment->answers()
                ->pluck('answer_value', 'question_id')
                ->map(function ($value) {
                    return json_decode($value, true);
                })
                ->toArray();

            // Generate new diet chart
            $result = $dietChartService->generateDietChart($assessment, $answers);

            if ($result['success']) {
                // Decrement user's purchased plans
                $this->decrementPurchasedPlans();

                return redirect()->route('child-profile')
                    ->with('success', 'Diet plan regenerated successfully!');
            } else {
                return redirect()->route('child-profile')
                    ->with('error', 'Failed to regenerate diet plan: ' . ($result['message'] ?? 'Unknown error'));
            }

        } catch (\Exception $e) {
            Log::error('Diet Chart Regeneration Error: ' . $e->getMessage());
            return redirect()->route('child-profile')
                ->with('error', 'Error regenerating diet plan');
        }
    }

    /**
     * Format diet plan meals for display.
     * Retrieves meals from database and organizes by day and slot.
     * 
     * @param DietPlan $dietPlan
     * @param ChildAssessment $assessment
     * @return array Formatted 7-day plan
     */
    protected function formatSevenDayPlan(DietPlan $dietPlan, ChildAssessment $assessment): array
    {
        $sevenDayPlan = [];
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        // Eager load all relationships to avoid N+1 queries
        $meals = $dietPlan->meals()
            ->with([
                'items.food'
            ])
            ->orderBy('sort_order')
            ->get();

        $slotsPerDay = match ($assessment->meal_pattern) {
            '3 meals' => 3,
            '2 + snacks' => 4,
            default => 5,
        };
        $slotOrder = ['Breakfast' => 1, 'Morning Snack' => 2, 'Lunch' => 3, 'Evening Snack' => 4, 'Snack' => 4, 'Dinner' => 5];

        // Group meals by day using the actual plan's meal-pattern, never a fixed four-meal assumption.
        $mealsByDay = [];
        $dayIndex = 0;

        foreach ($meals as $mealIndex => $meal) {
            $dayIndex = intdiv($mealIndex, $slotsPerDay);
            if ($dayIndex >= count($days)) {
                $dayIndex = count($days) - 1;
            }

            $currentDay = $days[$dayIndex];

            if (!isset($mealsByDay[$currentDay])) {
                $mealsByDay[$currentDay] = [];
            }

            // Calculate nutrient totals for this meal
            $totalNutrients = [
                'energy_kcal' => 0,
                'protein_g' => 0,
                'calcium_mg' => 0,
                'iron_mg' => 0,
                'zinc_mg' => 0,
                'vitamin_c_mg' => 0,
                'vitamin_d_mcg' => 0,
            ];

            $items = [];

            if ($meal->items) {
                foreach ($meal->items as $item) {
                    $food = $item->food;
                    $nutrients = $food->nutrients;

                    $foodItem = [
                        'food_id' => $food->id,
                        'food_name' => $food->food_name,
                        'regional_name' => $food->regional_name,
                        'portion_g' => $item->portion_size ?? ($food->serving_g ?? 150),
                        'nutrients' => [
                            'energy_kcal' => $nutrients->energy_kcal ?? 0,
                            'protein_g' => $nutrients->protein_g ?? 0,
                            'calcium_mg' => $nutrients->calcium_mg ?? 0,
                            'iron_mg' => $nutrients->iron_mg ?? 0,
                            'zinc_mg' => $nutrients->zinc_mg ?? 0,
                            'vitamin_c_mg' => $nutrients->vitamin_c_mg ?? 0,
                        ],
                        'pairing_notes' => $this->generatePairingNotes($food, $assessment),
                    ];

                    // Add to totals
                    foreach ($foodItem['nutrients'] as $key => $value) {
                        $totalNutrients[$key] += $value;
                    }

                    $items[] = $foodItem;
                }
            }

            $mealsByDay[$currentDay][$meal->meal_slot] = [
                'items' => $items,
                'total_nutrients' => $totalNutrients,
                'focus' => $meal->focus,
            ];
        }

        foreach ($mealsByDay as &$dayMeals) {
            uksort($dayMeals, fn ($a, $b) => ($slotOrder[$a] ?? 99) <=> ($slotOrder[$b] ?? 99));
        }

        return $mealsByDay;
    }

    /**
     * Generate clinical pairing notes for a food item.
     * 
     * @param $food
     * @param ChildAssessment $assessment
     * @return array Pairing notes
     */
    protected function generatePairingNotes($food, ChildAssessment $assessment): array
    {
        $notes = [];
        $nutrients = $food->nutrients;

        // Iron-rich food pairing
        if ($nutrients->iron_mg > 2) {
            $notes[] = 'Rich in iron. Serve with lemon juice or amla (Vitamin C) to enhance absorption by up to 3x';
        }

        // Calcium-rich food pairing
        if ($nutrients->calcium_mg > 200) {
            $notes[] = 'Excellent calcium source. Keep separate from iron-rich foods by 2+ hours for better absorption';
        }

        // Zinc-rich food pairing
        if ($nutrients->zinc_mg > 1) {
            $notes[] = 'Good zinc content. Pair with vitamin A/C rich vegetables for better absorption';
        }

        return $notes;
    }

    /**
     * Get clinical tips based on assessment and gaps.
     * 
     * @param ChildAssessment $assessment
     * @param array $gaps
     * @return array Clinical tips
     */
    protected function getClinicalTips(ChildAssessment $assessment, array $gaps): array
    {
        $tips = [];

        // Get tips from database based on goals/flags
        $comboEngine = app('App\Services\Nutrition\ComboEngine');
        $flags = $assessment->clinicalFlags()->pluck('flag_name')->toArray();

        $dbTips = $comboEngine->getCombos($assessment->main_goal ?? 'General Growth', $flags);

        foreach ($dbTips as $tip) {
            $tips[] = [
                'benefit' => $tip->benefit ?? 'Nutrition Tip',
                'combination' => $tip->combination ?? '',
                'science_reason' => $tip->science_reason ?? '',
            ];
        }

        // Add gap-specific tips
        foreach ($gaps as $nutrient => $gap) {
            if ($gap['gap_category'] === 'Critical gap') {
                if ($nutrient === 'iron_mg') {
                    $tips[] = [
                        'benefit' => 'Iron & Anemia Prevention',
                        'combination' => 'Iron-rich foods + Vitamin C source (lemon, amla, orange)',
                        'science_reason' => 'Vitamin C increases non-heme iron absorption from ~20% to ~60%, effectively tripling bioavailability',
                    ];
                } elseif ($nutrient === 'calcium_mg') {
                    $tips[] = [
                        'benefit' => 'Bone & Teeth Development',
                        'combination' => 'Calcium sources + Vitamin D exposure (15 min sun daily)',
                        'science_reason' => 'Vitamin D activates calcium-binding proteins in intestines, increasing absorption from 30% to 65%',
                    ];
                } elseif ($nutrient === 'zinc_mg') {
                    $tips[] = [
                        'benefit' => 'Immune Function & Growth',
                        'combination' => 'Zinc-rich foods + vitamin A/C vegetables',
                        'science_reason' => 'Zinc absorption is enhanced by vitamin A and inhibited by phytates; pair with vitamin-rich vegetables',
                    ];
                }
            }
        }

        return $tips;
    }

    /**
     * Get count of user's purchased but unused plans.
     * 
     * @return int
     */
    protected function getUserPurchasedPlansCount(): int
    {
        // DEMO ONLY: Bypass purchased plan check to prevent SQL error since plans_remaining column doesn't exist yet
        return 1;
    }

    /**
     * Decrement user's purchased plans count.
     * 
     * @return void
     */
    protected function decrementPurchasedPlans(): void
    {
        $userId = Auth::id();

        if ($userId) {
            $subscription = DietSubscription::where('user_id', $userId)
                ->where('status', 'active')
                ->where('plans_remaining', '>', 0)
                ->first();

            if ($subscription) {
                $subscription->decrement('plans_remaining');
            }
        }
    }

    /**
     * Authorize child access (user can only view their own child's data).
     * 
     * @param Child $child
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function authorizeChildAccess(Child $child): void
    {
        if ($child->user_id !== Auth::id()) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized access to child profile');
        }
    }

    /**
     * Download diet plan as PDF.
     * 
     * @param Child $child
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf(Child $child)
    {
        try {
            $this->authorizeChildAccess($child);

            $assessment = $child->assessments()->latest()->first();
            $dietPlan = $assessment->dietPlans()->where('status', 'active')->latest()->first();

            if (!$dietPlan) {
                return redirect()->back()->with('error', 'No active diet plan found');
            }

            return redirect()->back()->with('info', 'PDF generation coming soon!');

        } catch (\Exception $e) {
            Log::error('PDF Download Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error downloading PDF');
        }
    }

    /**
     * Display the dynamic 7-day diet chart on the meal-plan page.
     */
    public function mealPlan(Request $request)
    {
        try {
            $childId = $request->query('child_id');
            if ($childId) {
                $child = Child::where('id', $childId)->where('user_id', Auth::id() ?? 1)->first();
            } else {
                $child = Child::where('user_id', Auth::id() ?? 1)->latest()->first();
            }

            if (!$child) {
                return view('pages.user-panel.meal-plan');
            }

            $assessment = $child->assessments()->latest()->first();
            if (!$assessment) {
                return view('pages.user-panel.meal-plan');
            }

            $dietPlan = $assessment->dietPlans()->where('status', 'active')->where('review_status', 'approved')->latest()->first();
            if (!$dietPlan) {
                $reviewPending = $assessment->dietPlans()->where('status', 'pending_review')->exists();

                return view('pages.user-panel.meal-plan', compact('reviewPending'));
            }

            $sevenDayPlan = $this->formatSevenDayPlan($dietPlan, $assessment);
            
            $daysMap = [
                'Monday' => 'mon', 'Tuesday' => 'tue', 'Wednesday' => 'wed', 
                'Thursday' => 'thu', 'Friday' => 'fri', 'Saturday' => 'sat', 'Sunday' => 'sun'
            ];
            
            $focusList = ['IMMUNITY BOOST', 'GROWTH & BONES', 'BRAIN POWER', 'ENERGY DAY', 'BONE STRENGTH', 'REST & RESTORE'];
            
            $jsDays = [];
            $i = 0;
            foreach ($sevenDayPlan as $dayName => $slots) {
                if (!isset($daysMap[$dayName])) continue;
                $shortDay = $daysMap[$dayName];
                $meals = [];
                
                foreach ($slots as $slotName => $data) {
                    $mealItems = [];
                    foreach ($data['items'] as $item) {
                        $mealItems[] = $item['food_name'];
                    }
                    if (empty($mealItems)) continue;

                    $meals[] = [
                        'time' => $slotName,
                        'emoji' => '🍲',
                        'name' => implode(' + ', $mealItems),
                        'desc' => $data['focus'] ?? 'Healthy and nutritious meal.',
                        'tags' => ['Nutrition:protein']
                    ];
                }
                
                $jsDays[$shortDay] = [
                    'label' => $dayName,
                    'focus' => $focusList[$i % count($focusList)], // Cycle through focus areas for colors
                    'meals' => $meals
                ];
                $i++;
            }

            // Only pass if we actually generated valid data
            if (!empty($jsDays)) {
                return view('pages.user-panel.meal-plan', ['jsDays' => $jsDays]);
            }

            return view('pages.user-panel.meal-plan');

        } catch (\Exception $e) {
            Log::error('Meal Plan Error: ' . $e->getMessage());
            return view('pages.user-panel.meal-plan');
        }
    }
}
