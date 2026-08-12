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
