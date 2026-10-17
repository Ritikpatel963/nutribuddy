<?php

use App\Http\Controllers\Frontend\Nutrition\AssessmentController;
use App\Http\Controllers\Frontend\Checkout\CartController as FrontendCartController;
use App\Http\Controllers\Frontend\Checkout\CheckoutController as FrontendCheckoutController;
use App\Http\Controllers\Frontend\Storefront\ProductReviewController;
use App\Http\Controllers\Frontend\Account\UserAddressController;
use App\Http\Controllers\Frontend\Account\UserCouponController;
use App\Http\Controllers\Frontend\Account\UserOrderController;
use App\Http\Controllers\Frontend\Account\UserInvoiceController;
use App\Http\Controllers\Frontend\Account\UserProfileController;
use App\Http\Controllers\Frontend\Account\UserReturnController;
use App\Http\Controllers\Frontend\Account\UserSupportTicketController;
use App\Http\Controllers\Frontend\Account\UserWalletController;
use App\Http\Controllers\Frontend\Nutrition\DietSubscriptionInvoiceController;
use App\Http\Controllers\Frontend\Nutrition\NutritionController;
use App\Http\Controllers\Frontend\Nutrition\NutritionBackupController;
use App\Http\Controllers\Frontend\Nutrition\MealPlanController;
use App\Http\Controllers\Frontend\Storefront\SupplementController;
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
        Route::get('/{order}/invoice-page', [UserInvoiceController::class, 'page'])->name('invoice-page');
        Route::get('/{order}/invoice-download', [UserInvoiceController::class, 'download'])->name('invoice-download');
        Route::get('/{order}', [UserOrderController::class, 'show'])->name('show');
        Route::get('/{order}/invoice', [UserInvoiceController::class, 'show'])->name('invoice');
        Route::patch('/{order}/cancel', [UserOrderController::class, 'cancel'])->name('cancel');
        Route::post('/{order}/returns', [UserReturnController::class, 'store'])->name('returns.store');
    });

    Route::prefix('/user/invoices')->name('user.invoices.')->group(function () {
        Route::get('/', [UserInvoiceController::class, 'index'])->name('index');
    });

    Route::get('/my-coupons', [UserCouponController::class, 'index'])->name('my-coupons');

    Route::view('/change-password', 'frontend.account.change-password')->name('change-password');
    Route::view('/order', 'frontend.account.orders.index')->name('order');
    Route::get('/personal-info', [UserAddressController::class, 'index'])->name('personal-info');

    Route::post('/personal-info', [UserProfileController::class, 'update'])->name('personal-info.update');

    Route::get('/subscription', function () {
        $subscriptions = auth()->user()->dietSubscriptions()->with('child', 'product')->latest()->get();
        return view('frontend.account.subscriptions', compact('subscriptions'));
    })->name('subscription');
    Route::get('/diet-subscriptions/{subscription}/invoice-download', [DietSubscriptionInvoiceController::class, 'download'])->name('diet-subscriptions.invoice-download');
    Route::view('/user-return', 'frontend.account.returns.show')->name('user-return');
    Route::view('/userdashboard', 'frontend.account.dashboard')->name('userdashboard');
    Route::get('/nutrition', [NutritionController::class, 'nutrition'])->name('nutrition.index');
    Route::get('/nutrition-backup', [NutritionBackupController::class, 'nutritionBackup'])->name('nutrition.backup');
    Route::get('/meal-plan', [MealPlanController::class, 'mealPlan'])->name('meal-plan');
    Route::get('/supplement', [SupplementController::class, 'index'])->name('supplement');
    Route::get('/child-profile', [\App\Http\Controllers\Frontend\Nutrition\ChildProfileController::class, 'userPanelShow'])->name('child-profile');
    Route::get('/child-profile/create', [\App\Http\Controllers\Frontend\Nutrition\ChildProfileController::class, 'create'])->name('child-profile.create');
    Route::post('/child-profile', [\App\Http\Controllers\Frontend\Nutrition\ChildProfileController::class, 'store'])->name('child-profile.store');
    Route::get('/growth-signal', function () {
        $child = auth()->user()->children()->latest()->first();
        $plan = $child?->assessments()->latest()->first()?->dietPlan;
        return view('frontend.nutrition.growth-signal', compact('child', 'plan'));
    })->name('growth-signal');
    Route::view('/check-in', 'frontend.nutrition.check-in')->name('check-in');

    Route::get('/wallet', [UserWalletController::class, 'index'])->name('wallet');
    Route::get('/support-tickets', [UserSupportTicketController::class, 'index'])->name('user.support-tickets');
    Route::post('/support-tickets', [UserSupportTicketController::class, 'store'])->name('user.support-tickets.store');
    Route::get('/support-tickets/{ticket}', [UserSupportTicketController::class, 'show'])->name('user.support-tickets.show');
    Route::post('/support-tickets/{ticket}/reply', [UserSupportTicketController::class, 'reply'])->name('user.support-tickets.reply');
    Route::get('/user/reviews', [ProductReviewController::class, 'userIndex'])->name('user.reviews.index');
    Route::post('/product/{product}/reviews', [ProductReviewController::class, 'store'])->name('reviews.store');

    // ── Health Assessment ────────────────────────────────────────────────────
    Route::prefix('/user/assessment')->name('user.assessment.')->group(function () {
        Route::get('/', [AssessmentController::class, 'index'])->name('index');
        Route::post('/', [AssessmentController::class, 'store'])->name('store');
        Route::get('/history', [AssessmentController::class, 'history'])->name('history');
        Route::get('/compare', [AssessmentController::class, 'compare'])->name('compare');
        Route::get('/{attempt}/result', [AssessmentController::class, 'result'])->name('result');
        Route::get('/{attempt}/detail', [AssessmentController::class, 'detail'])->name('detail');
    });
});
