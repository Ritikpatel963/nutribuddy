<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\DietChartDisplayController;

/**
 * Frontend Diet Chart Display Routes
 * 
 * Base URL: /child/{child}
 * Prefix: These routes are for authenticated users to view their child's diet chart
 */

Route::middleware(['auth'])->group(function () {
    
    // Display diet chart for a child
    // GET /child/{child}/diet-chart
    Route::get('/diet-chart', [DietChartDisplayController::class, 'show'])
        ->name('child.diet-chart.show')
        ->where('child', '[0-9]+');

    // Regenerate diet chart
    // POST /child/{child}/diet-chart/regenerate
    Route::post('/diet-chart/regenerate', [DietChartDisplayController::class, 'regenerate'])
        ->name('child.diet-chart.regenerate')
        ->where('child', '[0-9]+');

    // Download diet chart as PDF
    // GET /child/{child}/diet-chart/download-pdf
    Route::get('/diet-chart/download-pdf', [DietChartDisplayController::class, 'downloadPdf'])
        ->name('child.diet-chart.download-pdf')
        ->where('child', '[0-9]+');
});

/**
 * Route Usage Examples:
 * 
 * In views:
 * {{ route('child.diet-chart.show', $child) }}
 * → /child/1/diet-chart
 * 
 * {{ route('child.diet-chart.regenerate', $child) }}
 * → /child/1/diet-chart/regenerate (POST)
 * 
 * {{ route('child.diet-chart.download-pdf', $child) }}
 * → /child/1/diet-chart/download-pdf
 */
