<?php

namespace App\Services\Nutrition;

use App\Models\Nutrition\ChildAssessment;
use App\Models\Nutrition\QuestionnaireQuestion;
use Carbon\Carbon;

class ChildProfileService
{
    public function normalize(ChildAssessment $assessment, array $answers = []): array
    {
        $answers = $this->byQuestionOrder($answers);
        $child = $assessment->child;
        $weight = (float) ($answers[4] ?? $assessment->current_weight_kg ?? 0);
        $height = (float) ($answers[5] ?? $assessment->current_height_cm ?? 0);
        $bmi = $height > 0 ? round($weight / (($height / 100) ** 2), 2) : null;
        $medicalCondition = $this->text($answers[12] ?? null);
        $profile = [
            'age_years' => $child?->dob ? Carbon::parse($child->dob)->age : null,
            'age_months' => $child?->dob ? Carbon::parse($child->dob)->diffInMonths(now()) : null,
            'gender' => strtolower((string) ($answers[3] ?? $child?->sex ?? '')),
            'weight_kg' => $weight ?: null,
            'height_cm' => $height ?: null,
            'bmi' => $bmi,
            'diet_type' => $this->dietType($answers[6] ?? $assessment->diet_type),
            'allergies' => $this->allergies($answers[7] ?? $assessment->allergies),
            'region' => $this->region($answers[8] ?? $assessment->region),
            'meal_pattern' => $this->mealPattern($answers[9] ?? $assessment->meal_pattern),
            'dairy_glasses' => $this->dairyGlasses($answers[10] ?? $assessment->dairy_glasses),
            'cooking_medium' => $this->text($answers[11] ?? $assessment->cooking_medium),
            'medical_condition' => $medicalCondition,
            // No growth reference table exists yet, so no medical threshold is inferred.
            'growth_profile' => 'unknown',
            'professional_review_required' => $this->requiresReview($medicalCondition),
        ];

        $assessment->fill([
            'current_weight_kg' => $profile['weight_kg'], 'current_height_cm' => $profile['height_cm'],
            'diet_type' => $profile['diet_type'], 'allergies' => $profile['allergies'], 'region' => $profile['region'],
            'meal_pattern' => $profile['meal_pattern'], 'dairy_glasses' => $profile['dairy_glasses'],
            'cooking_medium' => $profile['cooking_medium'], 'medical_condition' => $profile['medical_condition'],
            'bmi' => $profile['bmi'], 'growth_profile' => $profile['growth_profile'],
            'professional_review_required' => $profile['professional_review_required'],
        ])->save();

        return $profile;
    }

    private function byQuestionOrder(array $answers): array
    {
        $ids = array_filter(array_keys($answers), 'is_numeric');
        if ($ids === []) return $answers;
        $orders = QuestionnaireQuestion::whereIn('id', $ids)->pluck('sort_order', 'id');
        foreach ($orders as $id => $order) $answers[$order] = $answers[$id];
        return $answers;
    }

    private function dietType(mixed $value): string
    {
        return match (strtolower($this->text($value))) {
            'veg', 'vegetarian' => 'vegetarian', 'vegan' => 'vegan', 'non-veg', 'non veg', 'non-vegetarian' => 'non-vegetarian',
            'eggetarian', 'eggitarian' => 'eggetarian', 'jain' => 'jain', default => 'vegetarian',
        };
    }

    private function allergies(mixed $value): array
    {
        $values = is_array($value) ? $value : preg_split('/[,|]/', (string) $value);
        return collect($values)->map(fn ($item) => strtolower(trim((string) $item)))->map(function ($item) {
            return match (true) {
                str_contains($item, 'lactose') => 'lactose', str_contains($item, 'dairy') => 'dairy',
                str_contains($item, 'nut') || str_contains($item, 'seed') => 'nuts', str_contains($item, 'gluten') || str_contains($item, 'wheat') => 'gluten',
                str_contains($item, 'egg') => 'egg', default => null,
            };
        })->filter()->unique()->values()->all();
    }

    private function region(mixed $value): string
    {
        return match (strtolower($this->text($value))) {
            'north' => 'North', 'south' => 'South', 'east' => 'East', 'west' => 'West', default => 'No preference',
        };
    }

    private function mealPattern(mixed $value): string
    {
        return match (strtolower($this->text($value))) {
            '3 meals' => '3 meals', '2 + snacks', '2+snacks' => '2 + snacks', default => '3 meals + 2 snacks',
        };
    }

    private function dairyGlasses(mixed $value): int
    {
        return min(3, max(0, (int) preg_replace('/\D.*/', '', (string) $this->text($value))));
    }

    private function requiresReview(?string $condition): bool
    {
        return $condition !== null && !in_array(strtolower($condition), ['none', 'no', 'n/a', 'nil'], true);
    }

    private function text(mixed $value): ?string
    {
        $value = is_array($value) ? implode(', ', $value) : trim((string) $value);
        return $value === '' ? null : $value;
    }
}
