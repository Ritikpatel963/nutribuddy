<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Nutrition\QuestionnaireController;
use App\Http\Controllers\Api\Nutrition\DietPlanController;

Route::prefix('/api/nutrition')->name('api.nutrition.')->group(function () {
    // Dynamic Questionnaire Endpoints
    Route::get('/questionnaire/{childId}', [QuestionnaireController::class, 'getQuestions'])->name('questionnaire.get');
    Route::post('/questionnaire/{childId}', [QuestionnaireController::class, 'submitAnswers'])->name('questionnaire.submit');
    
    // Diet Plan Generation Endpoints
    Route::post('/generate-plan/{assessmentId}', [DietPlanController::class, 'generate'])->name('plan.generate');
});
