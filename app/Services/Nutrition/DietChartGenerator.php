<?php

namespace App\Services\Nutrition;

use App\Models\Nutrition\ChildAssessment;
use App\Models\Nutrition\DietPlan;

class DietChartGenerator
{
    protected $promptBuilder;

    public function __construct(AIPromptBuilder $promptBuilder)
    {
        $this->promptBuilder = $promptBuilder;
    }

    /**
     * The final step of the pipeline that interfaces with the AI.
     */
    public function generate(
        ChildAssessment $assessment, 
        array $dailyMeals, 
        array $adequacyAnalysis, 
        $tips
    ) {
        $child = $assessment->child;
        $prompt = $this->promptBuilder->build($child, $assessment, $dailyMeals, $adequacyAnalysis, $tips);
        
        // 1. Call LLM (Placeholder for OpenAI/Gemini/Anthropic API call)
        $aiResponse = $this->callLLM($prompt);
        
        // 2. Parse AI Response (expecting structured JSON)
        $parsed = json_decode($aiResponse, true);
        
        // 3. Save to DB (DietPlan)
        $dietPlan = DietPlan::create([
            'child_assessment_id' => $assessment->id,
            'ai_summary' => $parsed['summary'] ?? '',
            'version' => 1 
        ]);
        
        // Return the parsed JSON response for the frontend to render immediately
        return $parsed;
    }

    private function callLLM($prompt)
    {
        // Mocking the LLM structured JSON response
        return json_encode([
            'summary' => 'This diet focuses on healthy weight gain and increasing iron intake. *Note: Nutrition values are estimates based on standard servings.*',
            'meals' => [],
            'tips' => []
        ]);
    }
}
