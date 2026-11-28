<?php

namespace App\Services\Nutrition;

use App\Models\DietSubscription;
use App\Models\Nutrition\Child;
use App\Models\Nutrition\ChildAssessment;
use App\Models\Nutrition\DietPlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\Nutrition\PortionScaler;

class DietChartDisplayService
{
    // ------------------------------------------------------------------
    // formatPlan — canonical formatter for plans of any length
    // ------------------------------------------------------------------

    /**
     * Returns a list of day objects, one per plan day:
     *
     *   [
     *     'index'  => 0,
     *     'label'  => 'Monday',   // weekday name derived from the date
     *     'date'   => '2026-11-03',
     *     'week'   => 1,          // intdiv(index, 7) + 1
     *     'meals'  => [ slot => [...same structure as before...] ]
     *   ]
     *
     * Date comes from plan_data['schedule']['start_date'] when present (new plans),
     * or from $dietPlan->created_at for old 7-day plans.
     */
    public function formatPlan(DietPlan $dietPlan, ChildAssessment $assessment): array
    {
        $planData = $this->decodePlanData($dietPlan);
        $schedule = $planData['schedule'] ?? null;

        if ($schedule && isset($schedule['start_date']) && isset($schedule['days'])) {
            $startDate = Carbon::parse($schedule['start_date']);
            $totalDays = (int) $schedule['days'];
        } else {
            // Old 7-day plan: derive start from the plan's creation date, clamp to 7 days.
            $startDate = Carbon::parse($dietPlan->created_at)->startOfDay();
            $totalDays = 7;
        }

        $meals = $dietPlan->meals()
            ->with(['items.food'])
            ->orderBy('sort_order')
            ->get();

        $slotsPerDay = match ($assessment->meal_pattern) {
            '3 meals'    => 3,
            '2 + snacks' => 4,
            default      => 5,
        };

        $slotOrder = ['Breakfast' => 1, 'Morning Snack' => 2, 'Lunch' => 3, 'Evening Snack' => 4, 'Snack' => 4, 'Dinner' => 5];

        // Group meals by day_index.
        // New plans store day_index on each DietPlanMeal row.
        // Old plans do not have it; fall back to positional intdiv so that
        // meal 0 → day 0, meal slotsPerDay → day 1, etc.  No clamp is applied:
        // the position-based path is naturally bounded because the meal count
        // equals totalDays × slotsPerDay, so intdiv never produces an index
        // larger than totalDays - 1.
        $mealsByDayIndex = [];
        foreach ($meals as $mealIndex => $meal) {
            $idx = $meal->day_index ?? intdiv($mealIndex, $slotsPerDay);
            $mealsByDayIndex[$idx][] = $meal;
        }

        $days = [];
        for ($i = 0; $i < $totalDays; $i++) {
            $date        = $startDate->copy()->addDays($i);
            $weekdayName = $date->format('l');

            $slotData = [];
            foreach ($mealsByDayIndex[$i] ?? [] as $meal) {
                $totalNutrients = $this->emptyNutrients();
                $items          = [];

                foreach ($meal->items ?? [] as $item) {
                    $food    = $item->food;
                    // Use the stored portion_size (e.g. "217g") so scaled values
                    // are shown. PortionScaler returns ratio=1 when serving_g is
                    // missing or the portion cannot be parsed (safe fallback).
                    $portionG = $item->portion_size ?? ($food->serving_g ?? 150);
                    $scaledNutrients = PortionScaler::scale($food, $portionG);

                    $foodItem = [
                        'food_id'       => $food->id,
                        'food_name'     => $food->food_name,
                        'regional_name' => $food->regional_name,
                        'portion_g'     => $portionG,
                        'cook_min'      => $food->cook_min ?? 15,
                        'ingredients'   => $food->ingredient_list_per_serving,
                        'method'        => $food->method,
                        'nutrients'     => $this->extractNutrientsFromArray($scaledNutrients),
                        'pairing_notes' => $this->generatePairingNotes($food, $assessment),
                    ];

                    foreach ($foodItem['nutrients'] as $key => $value) {
                        if (isset($totalNutrients[$key])) {
                            $totalNutrients[$key] += $value;
                        }
                    }

                    $items[] = $foodItem;
                }

                $alternatives = $this->extractAlternatives($planData, $weekdayName, $meal->meal_slot);

                $slotData[$meal->meal_slot] = [
                    'items'           => $items,
                    'total_nutrients' => $totalNutrients,
                    'focus'           => $meal->focus,
                    'meal_id'         => $meal->id,
                    'alternatives'    => $alternatives,
                ];
            }

            // Sort slots within the day.
            uksort($slotData, fn ($a, $b) => ($slotOrder[$a] ?? 99) <=> ($slotOrder[$b] ?? 99));

            $days[] = [
                'index' => $i,
                'label' => $weekdayName,
                'date'  => $date->format('Y-m-d'),
                'week'  => intdiv($i, 7) + 1,
                'meals' => $slotData,
            ];
        }

        return $days;
    }

    /**
     * Keeps old callers working.
     * Returns the legacy day-keyed array (Monday … Sunday) for 7-day plans.
     */
    public function formatSevenDayPlan(DietPlan $dietPlan, ChildAssessment $assessment): array
    {
        $days   = $this->formatPlan($dietPlan, $assessment);
        $result = [];
        foreach ($days as $day) {
            $result[$day['label']] = $day['meals'];
        }
        return $result;
    }

    // ------------------------------------------------------------------
    // summarizeGaps — averages per-day gaps for old plan_data structures
    // ------------------------------------------------------------------

    /**
     * Accepts either the new flat per-nutrient summary (already has 'target',
     * 'absorbed_amount', 'adequacy_percent', 'gap_category') or the old
     * day-nested structure and averages it into the same shape.
     *
     * The output always contains:
     *   nutrient_key => ['target', 'absorbed_amount', 'adequacy_percent', 'gap_category']
     */
    public static function summarizeGaps(array $gaps): array
    {
        if (empty($gaps)) {
            return [];
        }

        // Detect new flat shape: first value has 'gap_category'.
        $first = reset($gaps);
        if (is_array($first) && array_key_exists('gap_category', $first)) {
            return $gaps;
        }

        // Old shape: $gaps is day-keyed (e.g. 'Monday' => ['iron_mg' => [...], ...])
        // or index-keyed (0 => ['iron_mg' => [...], ...]).
        $nutrients  = [];
        $dayCounts  = [];

        foreach ($gaps as $dayData) {
            if (! is_array($dayData)) {
                continue;
            }
            foreach ($dayData as $nutrient => $data) {
                if (! is_array($data) || ! isset($data['target'])) {
                    continue;
                }
                $nutrients[$nutrient]['target']            = $data['target'];
                $nutrients[$nutrient]['achieved_sum']      = ($nutrients[$nutrient]['achieved_sum'] ?? 0) + ($data['achieved'] ?? $data['absorbed_amount'] ?? 0);
                $nutrients[$nutrient]['adequacy_sum']      = ($nutrients[$nutrient]['adequacy_sum'] ?? 0) + ($data['adequacy_percent'] ?? 0);
                $dayCounts[$nutrient]                      = ($dayCounts[$nutrient] ?? 0) + 1;
            }
        }

        $summary = [];
        foreach ($nutrients as $nutrient => $data) {
            $count    = $dayCounts[$nutrient] ?? 1;
            $avgAchieved  = $data['achieved_sum'] / $count;
            $avgAdequacy  = $data['adequacy_sum']  / $count;
            $summary[$nutrient] = [
                'target'           => $data['target'],
                'absorbed_amount'  => round($avgAchieved, 4),
                'adequacy_percent' => round($avgAdequacy, 2),
                'gap_category'     => self::gapCategoryStatic($avgAdequacy),
            ];
        }

        return $summary;
    }

    // ------------------------------------------------------------------
    // Clinical helpers
    // ------------------------------------------------------------------

    protected function generatePairingNotes($food, ChildAssessment $assessment): array
    {
        $notes     = [];
        $nutrients = $food->nutrients;

        if (($nutrients->iron_mg ?? 0) > 2) {
            $notes[] = 'Rich in iron. Serve with lemon juice or amla (Vitamin C) to enhance absorption by up to 3x';
        }
        if (($nutrients->calcium_mg ?? 0) > 200) {
            $notes[] = 'Excellent calcium source. Keep separate from iron-rich foods by 2+ hours for better absorption';
        }
        if (($nutrients->zinc_mg ?? 0) > 1) {
            $notes[] = 'Good zinc content. Pair with vitamin A/C rich vegetables for better absorption';
        }

        return $notes;
    }

    public function getClinicalTips(ChildAssessment $assessment, array $gaps): array
    {
        $tips = [];

        $comboEngine = app('App\Services\Nutrition\ComboEngine');
        $flags       = $assessment->clinicalFlags()->pluck('flag_name')->toArray();

        // Pass child age and sex so ComboEngine can filter age-appropriate tips.
        $child = $assessment->child;
        $age   = $child?->dob ? \Carbon\Carbon::parse($child->dob)->age : null;
        $sex   = $child?->sex ?? null;     // 'boy' | 'girl'

        $dbTips = $comboEngine->getCombos(
            $assessment->main_goal ?? 'General Growth',
            $flags,
            $age,
            $sex
        );

        foreach ($dbTips as $tip) {
            $tips[] = [
                'benefit'        => $tip->benefit       ?? 'Nutrition Tip',
                'combination'    => $tip->combination   ?? '',
                'science_reason' => $tip->science_reason ?? '',
            ];
        }

        foreach ($gaps as $nutrient => $gap) {
            if (isset($gap['gap_category']) && $gap['gap_category'] === 'Critical gap') {
                if ($nutrient === 'iron_mg') {
                    $tips[] = [
                        'benefit'        => 'Iron & Anemia Prevention',
                        'combination'    => 'Iron-rich foods + Vitamin C source (lemon, amla, orange)',
                        'science_reason' => 'Vitamin C increases non-heme iron absorption from ~20% to ~60%, effectively tripling bioavailability',
                    ];
                } elseif ($nutrient === 'calcium_mg') {
                    $tips[] = [
                        'benefit'        => 'Bone & Teeth Development',
                        'combination'    => 'Calcium sources + Vitamin D exposure (15 min sun daily)',
                        'science_reason' => 'Vitamin D activates calcium-binding proteins in intestines, increasing absorption from 30% to 65%',
                    ];
                } elseif ($nutrient === 'zinc_mg') {
                    $tips[] = [
                        'benefit'        => 'Immune Function & Growth',
                        'combination'    => 'Zinc-rich foods + vitamin A/C vegetables',
                        'science_reason' => 'Zinc absorption is enhanced by vitamin A and inhibited by phytates; pair with vitamin-rich vegetables',
                    ];
                }
            }
        }

        return $tips;
    }

    // ------------------------------------------------------------------
    // Quota / access — unchanged method signatures for the controller
    // ------------------------------------------------------------------

    /**
     * Returns 1 when a chart can be generated now, otherwise 0.
     * The old plans_remaining counter is no longer used for generation gating.
     */
    public function getUserPurchasedPlansCount(Child $child): int
    {
        return $this->canGenerateChart($child) ? 1 : 0;
    }

    public function canGenerateChart(Child $child): bool
    {
        if (! $this->usableSubscriptionId($child)) {
            return false;
        }
        $next = $this->nextChartAvailableAt($child);
        return $next === null || $next->isPast();
    }

    /** When the next chart may be generated (null = right now, no chart exists yet). */
    public function nextChartAvailableAt(Child $child): ?Carbon
    {
        $last = DietPlan::query()
            ->whereIn('child_assessment_id', $child->assessments()->pluck('id'))
            ->latest()
            ->value('created_at');

        return $last ? Carbon::parse($last)->addMonthNoOverflow() : null;
    }

    /** Returns the subscription id that allows the generation, or null if not allowed now. */
    public function decrementPurchasedPlans(Child $child): ?int
    {
        return $this->canGenerateChart($child) ? $this->usableSubscriptionId($child) : null;
    }

    /** Nothing is consumed, so there is nothing to give back if generation fails. */
    public function restorePurchasedPlan(int $subscriptionId): void
    {
    }

    private function usableSubscriptionId(Child $child): ?int
    {
        $id = DietSubscription::query()
            ->where('user_id', Auth::id())
            ->where('child_id', $child->id)
            ->usable()
            ->orderByDesc('id')
            ->value('id');

        return $id ? (int) $id : null;
    }

    public function authorizeChildAccess(Child $child): void
    {
        if ($child->user_id !== Auth::id()) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized access to child profile');
        }
    }

    // ------------------------------------------------------------------
    // Private helpers
    // ------------------------------------------------------------------

    private function decodePlanData(DietPlan $dietPlan): array
    {
        $planData = $dietPlan->plan_data;
        if (is_string($planData)) {
            $planData = json_decode($planData, true) ?? [];
        }
        return is_array($planData) ? $planData : [];
    }

    private function emptyNutrients(): array
    {
        return [
            'energy_kcal'   => 0, 'protein_g'  => 0, 'carb_g'       => 0,
            'fat_g'         => 0, 'fibre_total_g' => 0, 'free_sugar_g' => 0,
            'sfa_g'         => 0, 'mufa_g'     => 0, 'pufa_g'       => 0,
            'calcium_mg'    => 0, 'iron_mg'    => 0, 'zinc_mg'      => 0,
            'magnesium_mg'  => 0, 'vit_c_mg'   => 0, 'vit_d_ug'     => 0,
            'b1_mg'         => 0, 'b2_mg'      => 0, 'b3_mg'        => 0,
            'b6_mg'         => 0, 'folate_ug'  => 0, 'b12_ug'       => 0,
        ];
    }

    private function extractNutrients(object $nutrients): array
    {
        return [
            'energy_kcal'   => $nutrients->energy_kcal   ?? 0,
            'protein_g'     => $nutrients->protein_g     ?? 0,
            'carb_g'        => $nutrients->carb_g        ?? 0,
            'fat_g'         => $nutrients->fat_g         ?? 0,
            'fibre_total_g' => $nutrients->fibre_total_g ?? 0,
            'free_sugar_g'  => $nutrients->free_sugar_g  ?? 0,
            'sfa_g'         => $nutrients->sfa_g         ?? 0,
            'mufa_g'        => $nutrients->mufa_g        ?? 0,
            'pufa_g'        => $nutrients->pufa_g        ?? 0,
            'calcium_mg'    => $nutrients->calcium_mg    ?? 0,
            'iron_mg'       => $nutrients->iron_mg       ?? 0,
            'zinc_mg'       => $nutrients->zinc_mg       ?? 0,
            'magnesium_mg'  => $nutrients->magnesium_mg  ?? 0,
            'vit_c_mg'      => $nutrients->vit_c_mg      ?? 0,
            'vit_d_ug'      => $nutrients->vit_d_ug      ?? 0,
            'b1_mg'         => $nutrients->b1_mg         ?? 0,
            'b2_mg'         => $nutrients->b2_mg         ?? 0,
            'b3_mg'         => $nutrients->b3_mg         ?? 0,
            'b6_mg'         => $nutrients->b6_mg         ?? 0,
            'folate_ug'     => $nutrients->folate_ug     ?? 0,
            'b12_ug'        => $nutrients->b12_ug        ?? 0,
        ];
    }

    /**
     * Same as extractNutrients() but accepts a pre-computed array
     * (e.g. the output of PortionScaler::scale()) instead of an object.
     * Uses the same new column-name keys.
     */
    private function extractNutrientsFromArray(array $nutrients): array
    {
        return [
            'energy_kcal'   => (float) ($nutrients['energy_kcal']   ?? 0),
            'protein_g'     => (float) ($nutrients['protein_g']     ?? 0),
            'carb_g'        => (float) ($nutrients['carb_g']        ?? 0),
            'fat_g'         => (float) ($nutrients['fat_g']         ?? 0),
            'fibre_total_g' => (float) ($nutrients['fibre_total_g'] ?? 0),
            'free_sugar_g'  => (float) ($nutrients['free_sugar_g']  ?? 0),
            'sfa_g'         => (float) ($nutrients['sfa_g']         ?? 0),
            'mufa_g'        => (float) ($nutrients['mufa_g']        ?? 0),
            'pufa_g'        => (float) ($nutrients['pufa_g']        ?? 0),
            'calcium_mg'    => (float) ($nutrients['calcium_mg']    ?? 0),
            'iron_mg'       => (float) ($nutrients['iron_mg']       ?? 0),
            'zinc_mg'       => (float) ($nutrients['zinc_mg']       ?? 0),
            'magnesium_mg'  => (float) ($nutrients['magnesium_mg']  ?? 0),
            'vit_c_mg'      => (float) ($nutrients['vit_c_mg']      ?? 0),
            'vit_d_ug'      => (float) ($nutrients['vit_d_ug']      ?? 0),
            'b1_mg'         => (float) ($nutrients['b1_mg']         ?? 0),
            'b2_mg'         => (float) ($nutrients['b2_mg']         ?? 0),
            'b3_mg'         => (float) ($nutrients['b3_mg']         ?? 0),
            'b6_mg'         => (float) ($nutrients['b6_mg']         ?? 0),
            'folate_ug'     => (float) ($nutrients['folate_ug']     ?? 0),
            'b12_ug'        => (float) ($nutrients['b12_ug']        ?? 0),
        ];
    }

    private function extractAlternatives(array $planData, string $weekdayName, string $slot): array
    {
        $alternatives = [];
        if (isset($planData['generation_debug']['meals'])) {
            foreach ($planData['generation_debug']['meals'] as $dm) {
                if ($dm['day'] === $weekdayName && $dm['slot'] === $slot) {
                    foreach ($dm['candidate_scores'] as $candidate) {
                        $altFood = \App\Models\Nutrition\FoodMaster::find($candidate['food_id']);
                        if ($altFood) {
                            $alternatives[] = [
                                'food_id'   => $altFood->id,
                                'food_name' => $altFood->food_name,
                                'energy_kcal' => round($altFood->nutrients->energy_kcal ?? 0),
                                'protein_g'   => round($altFood->nutrients->protein_g   ?? 0, 1),
                                'score'       => round($candidate['score'], 1),
                            ];
                        }
                    }
                    break;
                }
            }
        }
        return $alternatives;
    }

    private static function gapCategoryStatic(float $adequacy): string
    {
        if ($adequacy >= 90) return 'Met';
        if ($adequacy >= 70) return 'Partial gap';
        if ($adequacy >= 50) return 'Significant gap';
        return 'Critical gap';
    }

    /**
     * Consolidate ingredients for a given week (7 days) into grocery categories.
     */
    public function getWeeklyShoppingList(DietPlan $dietPlan, int $week = 1): array
    {
        $meals = $dietPlan->meals()->with('items.food')->get();
        $startDay = ($week - 1) * 7;
        $endDay = $startDay + 6;

        $categories = [
            'Grains & Flours' => [],
            'Dals & Pulses' => [],
            'Dairy' => [],
            'Vegetables & Greens' => [],
            'Spices, Oils & Other' => [],
        ];

        foreach ($meals as $meal) {
            $dayIdx = $meal->day_index ?? 0;
            if ($dayIdx < $startDay || $dayIdx > $endDay) {
                continue;
            }

            foreach ($meal->items ?? [] as $item) {
                $food = $item->food;
                $ingList = $food?->ingredient_list_per_serving;
                if (! $ingList) {
                    continue;
                }

                $parts = preg_split('/\s*·\s*|,\s*|;\s*/u', (string) $ingList);
                foreach ($parts as $part) {
                    $part = trim($part);
                    if ($part === '') {
                        continue;
                    }

                    $pLower = strtolower($part);
                    if (str_contains($pLower, 'atta') || str_contains($pLower, 'flour') || str_contains($pLower, 'rice') || str_contains($pLower, 'wheat') || str_contains($pLower, 'ragi') || str_contains($pLower, 'jowar') || str_contains($pLower, 'bajra') || str_contains($pLower, 'suji') || str_contains($pLower, 'poha') || str_contains($pLower, 'dalia') || str_contains($pLower, 'oats')) {
                        $cat = 'Grains & Flours';
                    } elseif (str_contains($pLower, 'dal') || str_contains($pLower, 'chana') || str_contains($pLower, 'rajma') || str_contains($pLower, 'chole') || str_contains($pLower, 'lobia') || str_contains($pLower, 'sprout') || str_contains($pLower, 'pulse') || str_contains($pLower, 'soybean') || str_contains($pLower, 'mung') || str_contains($pLower, 'urad') || str_contains($pLower, 'kulthi')) {
                        $cat = 'Dals & Pulses';
                    } elseif (str_contains($pLower, 'milk') || str_contains($pLower, 'paneer') || str_contains($pLower, 'curd') || str_contains($pLower, 'dahi') || str_contains($pLower, 'cheese') || str_contains($pLower, 'butter')) {
                        $cat = 'Dairy';
                    } elseif (str_contains($pLower, 'potato') || str_contains($pLower, 'onion') || str_contains($pLower, 'tomato') || str_contains($pLower, 'palak') || str_contains($pLower, 'spinach') || str_contains($pLower, 'gajar') || str_contains($pLower, 'carrot') || str_contains($pLower, 'methi') || str_contains($pLower, 'peas') || str_contains($pLower, 'matar') || str_contains($pLower, 'ginger') || str_contains($pLower, 'chilli') || str_contains($pLower, 'coriander') || str_contains($pLower, 'lemon') || str_contains($pLower, 'veg')) {
                        $cat = 'Vegetables & Greens';
                    } else {
                        $cat = 'Spices, Oils & Other';
                    }

                    $categories[$cat][$part] = ($categories[$cat][$part] ?? 0) + 1;
                }
            }
        }

        return array_filter($categories, fn ($items) => ! empty($items));
    }

    /**
     * Calculates the Child Health Score (0-100) across 6 clinical & dietary areas.
     */
    public function calculateHealthScore(ChildAssessment $assessment, array $nutrientGaps): array
    {
        // 1. Growth & BMI status (max 15)
        $bmi = (float) ($assessment->bmi ?? 16);
        $growthScore = ($bmi >= 14 && $bmi <= 18) ? 15 : (($bmi >= 13 && $bmi <= 20) ? 12 : 9);

        // 2. Calorie Adequacy (max 20)
        $energyAdequacy = (float) ($nutrientGaps['energy_kcal']['adequacy_percent'] ?? 100);
        $dietScore = ($energyAdequacy >= 90 && $energyAdequacy <= 115) ? 20 : (($energyAdequacy >= 75) ? 15 : 10);

        // 3. Protein & Micronutrients (max 20)
        $proteinAdequacy = (float) ($nutrientGaps['protein_g']['adequacy_percent'] ?? 100);
        $microScore = ($proteinAdequacy >= 90) ? 20 : (($proteinAdequacy >= 70) ? 15 : 10);

        // 4. Gut & Digestion (max 15)
        $gutScore = 15;
        $gutAnswer = strtolower((string) $assessment->answers()->whereHas('question', fn ($q) => $q->where('sort_order', 24))->value('answer_value'));
        if (str_contains($gutAnswer, 'constipat') || str_contains($gutAnswer, 'gas') || str_contains($gutAnswer, 'loose')) {
            $gutScore = 10;
        }

        // 5. Immunity & Resilience (max 15)
        $immScore = 15;
        $illAnswer = strtolower((string) $assessment->answers()->whereHas('question', fn ($q) => $q->where('sort_order', 25))->value('answer_value'));
        if (str_contains($illAnswer, 'frequent') || str_contains($illAnswer, 'often')) {
            $immScore = 9;
        }

        // 6. Lifestyle & Activity (max 15)
        $lifeScore = 15;
        $act = strtolower((string) $assessment->activity_level);
        if ($act === 'sedentary') {
            $lifeScore = 10;
        }

        $totalScore = $growthScore + $dietScore + $microScore + $gutScore + $immScore + $lifeScore;
        $grade = $totalScore >= 85 ? 'Excellent' : ($totalScore >= 70 ? 'Good' : 'Needs Attention');

        return [
            'total'     => min(100, $totalScore),
            'grade'     => $grade,
            'breakdown' => [
                'Growth & BMI Status'       => ['score' => $growthScore, 'max' => 15],
                'Calorie Adequacy'         => ['score' => $dietScore, 'max' => 20],
                'Protein & Micronutrients' => ['score' => $microScore, 'max' => 20],
                'Gut & Digestion'          => ['score' => $gutScore, 'max' => 15],
                'Immunity & Resilience'    => ['score' => $immScore, 'max' => 15],
                'Lifestyle & Activity'     => ['score' => $lifeScore, 'max' => 15],
            ],
        ];
    }
}
