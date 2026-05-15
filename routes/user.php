<?php

use App\Http\Controllers\Frontend\CartController as FrontendCartController;
use App\Http\Controllers\Frontend\CheckoutController as FrontendCheckoutController;
use App\Http\Controllers\Frontend\ProductReviewController;
use App\Http\Controllers\Frontend\UserAddressController;
use App\Http\Controllers\Frontend\UserCouponController;
use App\Http\Controllers\Frontend\UserOrderController;
use App\Http\Controllers\Frontend\UserProfileController;
use App\Http\Controllers\Frontend\UserReturnController;
use App\Http\Controllers\Frontend\UserSupportTicketController;
use App\Http\Controllers\Frontend\UserWalletController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('/user/cart')->name('user.cart.')->middleware('throttle:cart')->group(function () {
        Route::get('/', [FrontendCartController::class, 'index'])->name('index');
        Route::post('/', [FrontendCartController::class, 'store'])->name('store');
        Route::patch('/items/{itemId}', [FrontendCartController::class, 'update'])->name('items.update');
        Route::delete('/items/{itemId}', [FrontendCartController::class, 'destroy'])->name('items.destroy');
    });

    Route::prefix('/user/checkout')->name('user.checkout.')->middleware('throttle:checkout')->group(function () {
        Route::post('/summary', [FrontendCheckoutController::class, 'summary'])->name('summary');
        Route::post('/place-order', [FrontendCheckoutController::class, 'placeOrder'])->middleware('throttle:order-submit')->name('place-order');
    });

    Route::prefix('/user/addresses')->name('user.addresses.')->group(function () {
        Route::get('/', [UserAddressController::class, 'index'])->name('index');
        Route::get('/create', [UserAddressController::class, 'create'])->name('create');
        Route::post('/', [UserAddressController::class, 'store'])->name('store');
        Route::get('/{address}/edit', [UserAddressController::class, 'edit'])->name('edit');
        Route::patch('/{address}', [UserAddressController::class, 'update'])->name('update');
        Route::patch('/{address}/default', [UserAddressController::class, 'setDefault'])->name('set-default');
        Route::delete('/{address}', [UserAddressController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/user/orders')->name('user.orders.')->group(function () {
        Route::get('/', [UserOrderController::class, 'index'])->name('index');
        Route::get('/returns', [UserReturnController::class, 'index'])->name('returns.index');
        Route::get('/{order}/detail', [UserOrderController::class, 'detailPage'])->name('detail-page');
        Route::get('/{order}/invoice-page', [UserOrderController::class, 'invoicePage'])->name('invoice-page');
        Route::get('/{order}/invoice-download', [UserOrderController::class, 'invoiceDownload'])->name('invoice-download');
        Route::get('/{order}', [UserOrderController::class, 'show'])->name('show');
        Route::get('/{order}/invoice', [UserOrderController::class, 'invoice'])->name('invoice');
        Route::patch('/{order}/cancel', [UserOrderController::class, 'cancel'])->name('cancel');
        Route::post('/{order}/returns', [UserReturnController::class, 'store'])->name('returns.store');
    });

    Route::prefix('/user/invoices')->name('user.invoices.')->group(function () {
        Route::get('/', [UserOrderController::class, 'invoicesIndex'])->name('index');
    });

    Route::get('/my-coupons', [UserCouponController::class, 'index'])->name('my-coupons');

    Route::view('/change-password', 'pages.user-panel.change-password')->name('change-password');
    Route::view('/order', 'pages.user-panel.order')->name('order');
    Route::get('/personal-info', [UserAddressController::class, 'index'])->name('personal-info');

    Route::post('/personal-info', [UserProfileController::class, 'update'])->name('personal-info.update');

    Route::view('/subscription', 'pages.user-panel.subscription')->name('subscription');
    Route::view('/user-return', 'pages.user-panel.user-return')->name('user-return');
    Route::view('/userdashboard', 'pages.user-panel.userdashboard')->name('userdashboard');
    Route::view('/meal-plan', 'pages.user-panel.meal-plan')->name('meal-plan');
    Route::view('/health-scores', 'pages.user-panel.health-scores')->name('health-scores');
    Route::view('/supplement', 'pages.user-panel.supplement')->name('supplement');
    Route::view('/child-profile', 'pages.user-panel.child-profile')->name('child-profile');
    Route::view('/growth-signal', 'pages.user-panel.growth-signal')->name('growth-signal');
    Route::view('/check-in', 'pages.user-panel.check-in')->name('check-in');

    Route::get('/wallet', [UserWalletController::class, 'index'])->name('wallet');
    Route::get('/support-tickets', [UserSupportTicketController::class, 'index'])->name('user.support-tickets');
    Route::post('/support-tickets', [UserSupportTicketController::class, 'store'])->name('user.support-tickets.store');
    Route::get('/support-tickets/{ticket}', [UserSupportTicketController::class, 'show'])->name('user.support-tickets.show');
    Route::post('/support-tickets/{ticket}/reply', [UserSupportTicketController::class, 'reply'])->name('user.support-tickets.reply');
    Route::get('/user/reviews', [ProductReviewController::class, 'userIndex'])->name('user.reviews.index');
    Route::post('/product/{product}/reviews', [ProductReviewController::class, 'store'])->name('reviews.store');
});
