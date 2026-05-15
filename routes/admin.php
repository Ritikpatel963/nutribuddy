<?php

use App\Http\Controllers\Admin\AttributeController as AdminAttributeController;
use App\Http\Controllers\Admin\BlogCategoryController as AdminBlogCategoryController;
use App\Http\Controllers\Admin\BlogPostController as AdminBlogPostController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ContactLeadController as AdminContactLeadController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\IngredientCategoryController as AdminIngredientCategoryController;
use App\Http\Controllers\Admin\IngredientController as AdminIngredientController;
use App\Http\Controllers\Admin\NewsletterSubscriberController as AdminNewsletterSubscriberController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductVariantController as AdminProductVariantController;
use App\Http\Controllers\Admin\SupportTicketController as AdminSupportTicketController;
use App\Http\Controllers\Admin\TaxRateController as AdminTaxRateController;
use App\Http\Controllers\Frontend\UserOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:admin')->controller(DashboardController::class)->group(function () {
    Route::get('/admin', 'index')->name('admin.index');
    Route::get('/admin/export', 'export')->name('admin.dashboard.export');
    Route::get('/admin/analytics', 'analytics')->name('admin.analytics');
});

Route::prefix('admin/ecommerce')->name('admin.ecommerce.')->middleware('auth:admin')->group(function () {
    Route::resource('categories', AdminCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('attributes', AdminAttributeController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('ingredient-categories', AdminIngredientCategoryController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('products-trash', [AdminProductController::class, 'trash'])->name('products.trash');
    Route::delete('products-trash/bulk-force-delete', [AdminProductController::class, 'bulkForceDestroy'])->name('products.bulk-force-destroy');
    Route::patch('products-trash/{product}/restore', [AdminProductController::class, 'restore'])->name('products.restore');
    Route::delete('products-trash/{product}/force-delete', [AdminProductController::class, 'forceDestroy'])->name('products.force-destroy');
    Route::resource('products', AdminProductController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::patch('products/{product}/inventory', [AdminProductController::class, 'updateInventory'])->name('products.inventory.update');
    Route::delete('products/images/{image}', [AdminProductController::class, 'deleteImage'])->name('products.images.destroy');

    Route::resource('ingredients', AdminIngredientController::class);
    Route::resource('variants', AdminProductVariantController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::patch('variants/{variant}/inventory', [AdminProductVariantController::class, 'updateInventory'])->name('variants.inventory.update');
    Route::resource('coupons', AdminCouponController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('tax-rates', AdminTaxRateController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::resource('blog-categories', AdminBlogCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('blog-posts-trash', [AdminBlogPostController::class, 'trash'])->name('blog-posts.trash');
    Route::delete('blog-posts-trash/bulk-force-delete', [AdminBlogPostController::class, 'bulkForceDestroy'])->name('blog-posts.bulk-force-destroy');
    Route::patch('blog-posts-trash/{blogPost}/restore', [AdminBlogPostController::class, 'restore'])->name('blog-posts.restore');
    Route::delete('blog-posts-trash/{blogPost}/force-delete', [AdminBlogPostController::class, 'forceDestroy'])->name('blog-posts.force-destroy');
    Route::resource('blog-posts', AdminBlogPostController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::resource('contact-leads', AdminContactLeadController::class)->only(['index', 'update', 'destroy']);
    Route::get('newsletter/export', [AdminNewsletterSubscriberController::class, 'export'])->name('newsletter.export');
    Route::resource('newsletter', AdminNewsletterSubscriberController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('support-tickets', AdminSupportTicketController::class)->only(['index', 'store', 'update', 'destroy', 'show']);
    Route::post('support-tickets/{support_ticket}/reply', [AdminSupportTicketController::class, 'reply'])->name('support-tickets.reply');
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

    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::get('orders/{order}/invoice-download', [UserOrderController::class, 'invoiceDownload'])->name('orders.invoice-download');

    Route::get('side-section', [\App\Http\Controllers\Admin\SideSectionController::class, 'index'])->name('side-section.index');
    Route::post('side-section', [\App\Http\Controllers\Admin\SideSectionController::class, 'update'])->name('side-section.update');

    Route::prefix('loyalty')->name('loyalty.')->group(function () {
        Route::get('/settings', [\App\Http\Controllers\Admin\LoyaltyController::class, 'settings'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\Admin\LoyaltyController::class, 'updateSettings'])->name('settings.update');
        Route::get('/transactions', [\App\Http\Controllers\Admin\LoyaltyController::class, 'transactions'])->name('transactions');
    });
});
