<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Nutrition\DietChartController;

/**
 * 7-Day Pediatric Diet Chart Generation Routes
 * 
 * Base URL: /api/v1/nutrition/diet-chart
 */

Route::prefix('diet-chart')->group(function () {
    /**
     * POST /api/v1/nutrition/diet-chart/generate
     * 
     * Generate a 7-day diet chart from 12-question questionnaire answers.
     * 
     * Request body:
     * {
     *   "child_id": 1,
     *   "assessment_id": 1,
     *   "answers": {
     *     "1": "Child Name",
     *     "2": "2018-05-15",
     *     "3": "boy",
     *     "4": 25.5,
     *     "5": 115,
     *     "6": "vegetarian",
     *     "7": ["nuts", "dairy"],
     *     "8": "North",
     *     "9": 500,
     *     "10": 20,
     *     "11": "General Growth",
     *     "12": ["Pale eyes", "Frequent illness"]
     *   }
     * }
     * 
     * Response (201 Created):
     * {
     *   "success": true,
     *   "message": "7-day diet chart generated successfully",
     *   "data": {
     *     "diet_plan_id": 123,
     *     "child_id": 1,
     *     "assessment_id": 1,
     *     "icmr_targets": {...},
     *     "nutrient_gaps": {...},
     *     "daily_nutrients": {...},
     *     "seven_day_plan": {
     *       "Monday": {
     *         "Breakfast": {...},
     *         "Lunch": {...},
     *         "Snack": {...},
     *         "Dinner": {...}
     *       },
     *       ...
     *     },
     *     "clinical_tips": [...]
     *   }
     * }
     */
    Route::post('/', [DietChartController::class, 'store']);

    /**
     * GET /api/v1/nutrition/diet-chart/{assessmentId}/generate
     * 
     * Regenerate diet chart for an existing assessment.
     * Uses previously stored questionnaire answers.
     * 
     * Response (200 OK): Same as POST /diet-chart/
     */
    Route::get('{assessmentId}/generate', [DietChartController::class, 'generate']);

    /**
     * GET /api/v1/nutrition/diet-chart/{assessmentId}
     * 
     * Retrieve the generated 7-day diet plan details.
     * 
     * Response (200 OK):
     * {
     *   "success": true,
     *   "data": {
     *     "diet_plan_id": 123,
     *     "version": 1,
     *     "status": "active",
     *     "ai_summary": "...",
     *     "daily_nutrients": {...},
     *     "meals_by_day": {
     *       "Monday": [
     *         {
     *           "slot": "Breakfast",
     *           "timing": "07:00 - 08:00 AM",
     *           "focus": "...",
     *           "items": [...]
     *         }
     *       ]
     *     }
     *   }
     * }
     */
    Route::get('{assessmentId}', [DietChartController::class, 'show']);

    /**
     * GET /api/v1/nutrition/diet-chart/{assessmentId}/gaps
     * 
     * Get detailed nutrient gap analysis for the assessment.
     * Shows which nutrients have critical gaps, significant gaps, etc.
     * 
     * Response (200 OK):
     * {
     *   "success": true,
     *   "data": {
     *     "child_name": "Arjun",
     *     "age_years": 5,
     *     "sex": "boy",
     *     "icmr_targets": {...},
     *     "nutrient_gaps": {
     *       "iron_mg": {
     *         "nutrient": "iron_mg",
     *         "target": 9.0,
     *         "estimated_intake": 2.5,
     *         "absorbed_amount": 0.5,
     *         "bioavailability_factor": 0.2,
     *         "adequacy_percent": 5.56,
     *         "gap_category": "Critical gap",
     *         "gap_amount": 8.5
     *       },
     *       ...
     *     },
     *     "gap_summary": {
     *       "critical_gaps": 2,
     *       "significant_gaps": 1,
     *       "partial_gaps": 3,
     *       "met_nutrients": 13
     *     }
     *   }
     * }
     */
    Route::get('{assessmentId}/gaps', [DietChartController::class, 'gapAnalysis']);

    /**
     * GET /api/v1/nutrition/diet-chart/child/{childId}
     * 
     * List all diet plans for a child across all assessments.
     * 
     * Response (200 OK):
     * {
     *   "success": true,
     *   "child_id": 1,
     *   "child_name": "Arjun",
     *   "assessments": [
     *     {
     *       "assessment_id": 1,
     *       "model_level": "basic",
     *       "created_at": "2026-09-07T10:30:00Z",
     *       "latest_diet_plan": {
     *         "id": 123,
     *         "version": 1,
     *         "status": "active",
     *         "created_at": "2026-09-07T10:35:00Z"
     *       }
     *     }
     *   ]
     * }
     */
    Route::get('child/{childId}', [DietChartController::class, 'listByChild']);

    /**
     * PUT /api/v1/nutrition/diet-chart/{assessmentId}
     * 
     * Update assessment data and regenerate diet chart.
     * 
     * Request body (all fields optional):
     * {
     *   "current_weight_kg": 26.0,
     *   "current_height_cm": 120.5,
     *   "main_goal": "Weight Gain",
     *   "diet_type": "vegetarian",
     *   "allergies": ["nuts"],
     *   "region": "South",
     *   "budget": "moderate"
     * }
     * 
     * Response (200 OK): Same as POST /diet-chart/
     */
    Route::put('{assessmentId}', [DietChartController::class, 'update']);
});

/**
 * Error Responses
 * 
 * 400 Bad Request:
 * - Malformed request body
 * - Missing required fields
 * 
 * 422 Unprocessable Entity:
 * - Validation failed (invalid child_id, assessment_id, etc.)
 * - Child-assessment mismatch
 * 
 * 404 Not Found:
 * - Child not found
 * - Assessment not found
 * - Diet plan not found for assessment
 * 
 * 500 Internal Server Error:
 * - Diet chart generation failed
 * - Database query error
 * - Bioavailability calculation error
 * 
 * Example error response:
 * {
 *   "success": false,
 *   "error": "Validation failed",
 *   "errors": {
 *     "child_id": ["The child_id field is required."],
 *     "answers": ["The answers field must be an array."]
 *   }
 * }
 */
