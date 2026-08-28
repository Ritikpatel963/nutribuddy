<?php

namespace App\Services\Nutrition;

use App\Models\Nutrition\ChildAssessment;

class FoodFilterPipeline
{
    /**
     * Filters the central food database strictly based on child's safety restrictions.
     */
    public function getEligibleFoods(ChildAssessment $assessment, array $dislikedFoods = [], $currentSeason = 'Year-round')
    {
        $query = \App\Models\Nutrition\FoodMaster::where('active', true);
        
        // 1. Hard Filter: Diet Type
        if ($assessment->diet_type === 'vegan') {
            $query->where('vegan', true);
        } elseif ($assessment->diet_type === 'jain') {
            $query->where('jain_allowed', true);
        } elseif ($assessment->diet_type === 'vegetarian') {
            $query->where('veg_status', true)->where('contains_egg', false);
        }
        
        // 2. Hard Filter: Allergies
        $allergies = $assessment->allergies ?? [];
        if (in_array('dairy', $allergies)) {
            $query->where('contains_dairy', false);
        }
        if (in_array('nuts', $allergies)) {
            $query->where('contains_nuts', false);
        }
        if (in_array('gluten', $allergies)) {
            $query->where('contains_gluten', false);
        }
        if (in_array('egg', $allergies)) {
            $query->where('contains_egg', false);
        }
        
        // 3. Disliked Foods
        if (!empty($dislikedFoods)) {
            $query->whereNotIn('food_name', $dislikedFoods);
        }
        
        // 4. Season Filter (keep year-round and current season)
        $query->whereIn('season', ['Year-round', $currentSeason]);
        
        // 5. Budget Filtering
        if ($assessment->budget === 'economy') {
            $query->whereIn('cost_band', ['economy']);
        } elseif ($assessment->budget === 'moderate') {
            $query->whereIn('cost_band', ['economy', 'moderate']);
        }
        
        return $query->get();
    }
}
