<?php

namespace App\Services\Nutrition;

use App\Models\Nutrition\ChildAssessment;
use App\Models\Nutrition\DeficiencyRule;
use App\Models\Nutrition\ClinicalFlag;

class RiskFlaggingEngine
{
    /**
     * Correlates visible signs, bowel habits, and illnesses to generate flags.
     */
    public function generateFlags(ChildAssessment $assessment, array $answers)
    {
        // Example: $answers['visible_signs'] = ['Pale eyes/skin', 'Tiredness'];
        $signs = $answers['visible_signs'] ?? [];
        
        $rules = DeficiencyRule::whereIn('sign', $signs)->get();
        
        foreach ($rules as $rule) {
            ClinicalFlag::create([
                'child_assessment_id' => $assessment->id,
                'flag_name' => $rule->likely_gap,
                'reason' => 'Observed sign: ' . $rule->sign,
                'priority' => 'medium'
            ]);
        }
    }
}
