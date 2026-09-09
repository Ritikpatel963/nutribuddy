<?php

namespace App\Services\Nutrition;

use App\Models\Nutrition\ChildAssessment;

/** Backwards-compatible entry point for all existing chart endpoints. */
class DietChartGeneratorService
{
    public function __construct(private readonly RuleBasedDietPlanEngine $engine) {}

    public function generateDietChart(ChildAssessment $assessment, array $questionnaireAnswers = []): array
    {
        return $this->engine->generate($assessment, $questionnaireAnswers);
    }
}
