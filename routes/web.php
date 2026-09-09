<?php

use App\Http\Controllers\CsrfTokenController;
use App\Http\Controllers\Frontend\AiSensyWebhookController;
use App\Http\Controllers\Frontend\CashfreePaymentController;
use App\Http\Controllers\Frontend\CheckoutController as FrontendCheckoutController;
use App\Http\Controllers\StorageController;
use Illuminate\Support\Facades\Route;

Route::get('/csrf-token', [CsrfTokenController::class, 'show'])->name('csrf.token');

Route::get('/storage/{path}', [StorageController::class, 'showPublic'])
    ->where('path', '.*')
    ->name('storage.public');

Route::prefix('/checkout')->name('checkout.')->group(function () {
    Route::get('/', [FrontendCheckoutController::class, 'index'])->name('index');
    Route::post('/', [FrontendCheckoutController::class, 'store'])->name('store');
    Route::get('/cities/{stateCode}', [FrontendCheckoutController::class, 'getCities'])
        ->where('stateCode', '[A-Za-z0-9\-]+')
        ->name('cities');
});

use App\Http\Controllers\Frontend\RazorpayPaymentController;

Route::prefix('/payment/cashfree')->name('cashfree.')->group(function () {
    Route::get('/return', [CashfreePaymentController::class, 'return'])->name('return');
    Route::post('/webhook', [CashfreePaymentController::class, 'webhook'])->name('webhook');
});

Route::prefix('/payment/razorpay')->name('razorpay.')->group(function () {
    Route::post('/verify', [RazorpayPaymentController::class, 'verify'])->name('verify');
    Route::post('/webhook', [RazorpayPaymentController::class, 'webhook'])->name('webhook');
});

Route::post('/webhooks/aisensy', [AiSensyWebhookController::class, 'handle'])->name('aisensy.webhook');

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/storefront.php';
require __DIR__ . '/user.php';
require __DIR__ . '/nutrition.php';

use App\Http\Controllers\Frontend\Nutrition\QuestionnaireController;
use App\Http\Controllers\Frontend\ChildProfileController;
use App\Http\Controllers\Frontend\DietChartDisplayController;

Route::view('/personalized-diet-chart', 'frontend.nutrition.personalized_diet')->name('frontend.nutrition.personalized_diet');
Route::get('/nutrition/questionnaire', [QuestionnaireController::class, 'show'])->name('frontend.nutrition.questionnaire');

// Debug: Authorization check (remove in production)
Route::middleware(['auth'])->get('/debug/authorization', function () {
    return view('debug/authorization-check');
})->name('debug.authorization');

// Diet Chart Display Routes - nested under child profile
Route::middleware(['auth'])->prefix('/child/{child}')->group(function () {
    Route::get('/diet-chart', [DietChartDisplayController::class, 'show'])
        ->name('child.diet-chart.show')
        ->where('child', '[0-9]+');

    Route::post('/diet-chart/regenerate', [DietChartDisplayController::class, 'regenerate'])
        ->name('child.diet-chart.regenerate')
        ->where('child', '[0-9]+');

    Route::get('/diet-chart/download-pdf', [DietChartDisplayController::class, 'downloadPdf'])
        ->name('child.diet-chart.download-pdf')
        ->where('child', '[0-9]+');
});

Route::redirect('/child-profile/{child}', '/child-profile')->name('child.profile');
Route::redirect('/child-profile/{child}/diet-chart', '/child-profile')->name('child.diet_chart');
