<?php

namespace App\Http\Controllers\Api\Nutrition;

use App\Http\Controllers\Controller;
use App\Models\Nutrition\ChildAssessment;
use App\Services\Nutrition\DietChartGenerator;
use App\Services\Nutrition\FoodFilterPipeline;
use App\Services\Nutrition\TargetEngine;
use App\Services\Nutrition\MealBuilder;
use App\Services\Nutrition\NutritionCalculator;
use App\Services\Nutrition\AdequacyAnalyzer;
use App\Services\Nutrition\ComboEngine;
use App\Services\Nutrition\RiskFlaggingEngine;

class DietPlanController extends Controller
{
    protected $generator;
    protected $filter;
    protected $targetEngine;
    protected $mealBuilder;
    protected $nutritionCalculator;
    protected $adequacyAnalyzer;
    protected $comboEngine;
    protected $riskEngine;

    public function __construct(
        DietChartGenerator $generator,
        FoodFilterPipeline $filter,
        TargetEngine $targetEngine,
        MealBuilder $mealBuilder,
        NutritionCalculator $nutritionCalculator,
        AdequacyAnalyzer $adequacyAnalyzer,
        ComboEngine $comboEngine,
        RiskFlaggingEngine $riskEngine
    ) {
        $this->generator = $generator;
        $this->filter = $filter;
        $this->targetEngine = $targetEngine;
        $this->mealBuilder = $mealBuilder;
        $this->nutritionCalculator = $nutritionCalculator;
        $this->adequacyAnalyzer = $adequacyAnalyzer;
        $this->comboEngine = $comboEngine;
        $this->riskEngine = $riskEngine;
    }

    /**
     * Generate the diet plan using the unified 3-layer architecture.
     */
    public function generate($assessmentId)
    {
        $assessment = ChildAssessment::findOrFail($assessmentId);
        $child = $assessment->child;
        $answers = []; // Mock fetching answers
        
        // 1. Fetch ICMR Target
        $icmrTarget = $this->targetEngine->getTargets($child);
        
        // 2. Generate Risk Flags
        $this->riskEngine->generateFlags($assessment, $answers);
        $flags = $assessment->clinicalFlags()->pluck('flag_name')->toArray();
        
        // 3. Filter Eligible Food Pool (Hard Safety)
        $eligibleFoods = $this->filter->getEligibleFoods($assessment, [], 'Year-round');
        
        // 4. Build Meals
        $slots = ['Breakfast', 'Lunch', 'Snack', 'Dinner'];
        $dailyMeals = [];
        foreach ($slots as $slot) {
            $dailyMeals[$slot] = $this->mealBuilder->buildPlate($eligibleFoods, $slot);
        }
        
        // 5. Calculate Nutrition & Adequacy (Deterministic)
        $summedNutrition = $this->nutritionCalculator->sumDailyNutrition($dailyMeals);
        $adequacyAnalysis = $this->adequacyAnalyzer->analyze($summedNutrition, $icmrTarget);
        
        // 6. Fetch Tips & Combos
        $tips = $this->comboEngine->getCombos($assessment->main_goal, $flags);
        
        // 7. Layer 3: AI Presentation
        $plan = $this->generator->generate($assessment, $dailyMeals, $adequacyAnalysis, $tips);
        
        return response()->json([
            'message' => 'Unified Diet plan generated successfully',
            'plan' => $plan
        ]);
    }
}
