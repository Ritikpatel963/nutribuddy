<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\AuthController as FrontendAuthController;
use App\Http\Controllers\Frontend\CartController as FrontendCartController;
use App\Http\Controllers\Frontend\CheckoutController as FrontendCheckoutController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\UserAddressController;
use App\Http\Controllers\Frontend\UserOrderController;
use App\Http\Controllers\Frontend\UserReturnController;
use App\Http\Controllers\Admin\AuthenticationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BlogCategoryController as AdminBlogCategoryController;
use App\Http\Controllers\Admin\BlogPostController as AdminBlogPostController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ContactLeadController as AdminContactLeadController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\IngredientCategoryController as AdminIngredientCategoryController;
use App\Http\Controllers\Admin\IngredientController as AdminIngredientController;
use App\Http\Controllers\Admin\NewsletterSubscriberController as AdminNewsletterSubscriberController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductVariantController as AdminProductVariantController;
use App\Http\Controllers\Admin\SupportTicketController as AdminSupportTicketController;
use App\Http\Controllers\Admin\TaxRateController as AdminTaxRateController;

Route::prefix('authentication')->group(function () {
    Route::controller(AuthenticationController::class)->group(function () {
        Route::get('/forgotpassword', 'forgotPassword')->name('forgotPassword');
        Route::get('/signin', 'signin')->name('signin');
        Route::post('/login', 'login')->name('admin.login.post');
        Route::post('/logout', 'logout')->name('admin.logout');
        Route::get('/signup', 'signup')->name('signup');
    });
});

Route::name('frontend.')->group(function () {
    Route::controller(FrontendAuthController::class)->group(function () {
        Route::get('/login', 'showLogin')->name('login');
        Route::post('/send-otp', 'sendOtp')->name('sendOtp');
        Route::post('/verify-otp', 'verifyOtp')->name('verifyOtp');
        Route::post('/logout', 'logout')->name('logout');
    });
});

Route::middleware('auth:admin')->controller(DashboardController::class)->group(function () {
    Route::get('/admin', 'index')->name('admin.index');
});

Route::prefix('admin/ecommerce')->name('admin.ecommerce.')->middleware('auth:admin')->group(function () {
    Route::resource('categories', AdminCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('ingredient-categories', AdminIngredientCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('products', AdminProductController::class);
    Route::resource('ingredients', AdminIngredientController::class);
    Route::patch('products/{product}/inventory', [AdminProductController::class, 'updateInventory'])->name('products.inventory.update');
    Route::resource('variants', AdminProductVariantController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::patch('variants/{variant}/inventory', [AdminProductVariantController::class, 'updateInventory'])->name('variants.inventory.update');
    Route::resource('coupons', AdminCouponController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('tax-rates', AdminTaxRateController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('blog-categories', AdminBlogCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('blog-posts', AdminBlogPostController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('contact-leads', AdminContactLeadController::class)->only(['index', 'update', 'destroy']);
    Route::resource('newsletter', AdminNewsletterSubscriberController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('support-tickets', AdminSupportTicketController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class);
    Route::resource('reviews', \App\Http\Controllers\Admin\ReviewController::class)->only(['index', 'update', 'destroy']);
    Route::resource('order-returns', \App\Http\Controllers\Admin\OrderReturnController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::resource('invoices', \App\Http\Controllers\Admin\InvoiceController::class)->only(['index', 'show']);

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/general', [\App\Http\Controllers\Admin\GeneralSettingController::class, 'index'])->name('general');
        Route::post('/general', [\App\Http\Controllers\Admin\GeneralSettingController::class, 'update'])->name('general.update');
    });

    Route::delete('products/images/{image}', [AdminProductController::class, 'deleteImage'])->name('products.images.destroy');

    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

    Route::get('side-section', [\App\Http\Controllers\Admin\SideSectionController::class, 'index'])->name('side-section.index');
    Route::post('side-section', [\App\Http\Controllers\Admin\SideSectionController::class, 'update'])->name('side-section.update');
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'pages.about-us')->name('about');
Route::get('/product', [ProductController::class, 'index'])->name('product');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::view('/diet-chart', 'pages.diet-chart')->name('diet_chart');
Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/checkout', 'pages.checkout')->name('checkout');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/return-policy', 'pages.return-policy')->name('return-policy');
Route::view('/terms', 'pages.terms')->name('terms');
Route::view('/cart', 'pages.cart')->name('cart.page');

Route::prefix('/guest/checkout')->name('guest.checkout.')->group(function () {
    Route::post('/summary', [FrontendCheckoutController::class, 'guestSummary'])->name('summary');
});

Route::middleware('auth')->group(function () {
    Route::prefix('/user/cart')->name('user.cart.')->group(function () {
        Route::get('/', [FrontendCartController::class, 'index'])->name('index');
        Route::post('/', [FrontendCartController::class, 'store'])->name('store');
        Route::patch('/items/{itemId}', [FrontendCartController::class, 'update'])->name('items.update');
        Route::delete('/items/{itemId}', [FrontendCartController::class, 'destroy'])->name('items.destroy');
    });

    Route::prefix('/user/checkout')->name('user.checkout.')->group(function () {
        Route::post('/summary', [FrontendCheckoutController::class, 'summary'])->name('summary');
        Route::post('/place-order', [FrontendCheckoutController::class, 'placeOrder'])->name('place-order');
    });

    Route::prefix('/user/addresses')->name('user.addresses.')->group(function () {
        Route::get('/', [UserAddressController::class, 'index'])->name('index');
        Route::post('/', [UserAddressController::class, 'store'])->name('store');
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

    Route::view('/change-password', 'pages.user-panel.change-password')->name('change-password');
    Route::view('/order', 'pages.user-panel.order')->name('order');
    Route::view('/personal-info', 'pages.user-panel.personal-info')->name('personal-info');
    Route::view('/subscription', 'pages.user-panel.subscription')->name('subscription');
    Route::view('/user-return', 'pages.user-panel.user-return')->name('user-return');
    Route::view('/userdashboard', 'pages.user-panel.userdashboard')->name('userdashboard');
    Route::view('/meal-plan', 'pages.user-panel.meal-plan')->name('meal-plan');
    Route::view('/health-scores', 'pages.user-panel.health-scores')->name('health-scores');
    Route::view('/supplement', 'pages.user-panel.supplement')->name('supplement');
    Route::view('/child-profile', 'pages.user-panel.child-profile')->name('child-profile');
    Route::view('/growth-signal', 'pages.user-panel.growth-signal')->name('growth-signal');
    Route::view('/check-in', 'pages.user-panel.check-in')->name('check-in');
    Route::post('/product/{product}/reviews', [\App\Http\Controllers\ProductReviewController::class, 'store'])->name('reviews.store');
});
