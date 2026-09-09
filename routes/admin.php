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

// ── Dashboard & Analytics ─────────────────────────────────────────────────────
Route::middleware('auth:admin')->controller(DashboardController::class)->group(function () {
    Route::get('/admin', 'index')->name('admin.index');
    
    Route::middleware('permission:view dashboard|manage dashboard,admin')->group(function () {
        Route::get('/admin/export', 'export')->name('admin.dashboard.export');
    });

    Route::middleware('permission:view analytics|manage analytics,admin')->group(function () {
        Route::get('/admin/analytics', 'analytics')->name('admin.analytics');
    });
});

// ── Access Control (Users & Roles) ───────────────────────────────────────────
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    // Both users and roles are managed on the same index page. The user needs view access to either.
    Route::middleware('permission:view users|manage users|view roles|manage roles,admin')->group(function () {
        Route::get('access-control', [\App\Http\Controllers\Admin\AccessControlController::class, 'index'])->name('access-control.index');
    });

    Route::middleware('permission:manage roles,admin')->group(function () {
        Route::post('access-control/roles', [\App\Http\Controllers\Admin\AccessControlController::class, 'storeRole'])->name('access-control.roles.store');
        Route::put('access-control/roles/{role}', [\App\Http\Controllers\Admin\AccessControlController::class, 'updateRole'])->name('access-control.roles.update');
        Route::delete('access-control/roles/{role}', [\App\Http\Controllers\Admin\AccessControlController::class, 'destroyRole'])->name('access-control.roles.destroy');
    });

    Route::middleware('permission:manage users,admin')->group(function () {
        Route::post('access-control/users', [\App\Http\Controllers\Admin\AccessControlController::class, 'storeUser'])->name('access-control.users.store');
        Route::put('access-control/users/{admin}', [\App\Http\Controllers\Admin\AccessControlController::class, 'updateUser'])->name('access-control.users.update');
        Route::delete('access-control/users/{admin}', [\App\Http\Controllers\Admin\AccessControlController::class, 'destroyUser'])->name('access-control.users.destroy');
    });
});

// ── Ecommerce Modules ────────────────────────────────────────────────────────
Route::prefix('admin/ecommerce')->name('admin.ecommerce.')->middleware('auth:admin')->group(function () {
    
    // -- Products Management --
    Route::middleware('permission:view products|manage products,admin')->group(function () {
        Route::resource('categories', AdminCategoryController::class)->only(['index', 'show']);
        Route::resource('attributes', AdminAttributeController::class)->only(['index', 'show']);
        Route::resource('ingredient-categories', AdminIngredientCategoryController::class)->only(['index', 'show']);
        Route::get('products-trash', [AdminProductController::class, 'trash'])->name('products.trash');
        Route::get('products/problem-solution', [AdminProductController::class, 'problemSolutionIndex'])->name('products.problem-solution.index');
        Route::resource('products', AdminProductController::class)->only(['index', 'show']);
        Route::resource('ingredients', AdminIngredientController::class)->only(['index', 'show']);
        Route::resource('variants', AdminProductVariantController::class)->only(['index', 'show']);
        Route::resource('tax-rates', AdminTaxRateController::class)->only(['index', 'show']);
    });
    
    Route::middleware('permission:manage products,admin')->group(function () {
        Route::resource('categories', AdminCategoryController::class)->except(['index', 'show']);
        Route::resource('attributes', AdminAttributeController::class)->except(['index', 'show']);
        Route::resource('ingredient-categories', AdminIngredientCategoryController::class)->except(['index', 'show']);
        
        Route::delete('products-trash/bulk-force-delete', [AdminProductController::class, 'bulkForceDestroy'])->name('products.bulk-force-destroy');
        Route::patch('products-trash/{product}/restore', [AdminProductController::class, 'restore'])->name('products.restore');
        Route::delete('products-trash/{product}/force-delete', [AdminProductController::class, 'forceDestroy'])->name('products.force-destroy');
        Route::get('products/{product}/problem-solution', [AdminProductController::class, 'problemSolution'])->name('products.problem-solution.edit');
        Route::post('products/{product}/problem-solution', [AdminProductController::class, 'updateProblemSolution'])->name('products.problem-solution.update');
        Route::resource('products', AdminProductController::class)->except(['index', 'show']);
        Route::patch('products/{product}/inventory', [AdminProductController::class, 'updateInventory'])->name('products.inventory.update');
        Route::delete('products/images/{image}', [AdminProductController::class, 'deleteImage'])->name('products.images.destroy');
        
        Route::resource('ingredients', AdminIngredientController::class)->except(['index', 'show']);
        Route::resource('variants', AdminProductVariantController::class)->except(['index', 'show']);
        Route::patch('variants/{variant}/inventory', [AdminProductVariantController::class, 'updateInventory'])->name('variants.inventory.update');
        Route::resource('tax-rates', AdminTaxRateController::class)->except(['index', 'show']);
    });

    // -- Diet Chart --
    Route::middleware('permission:view diet chart|manage diet chart,admin')->group(function () {
        Route::resource('diet-plans', \App\Http\Controllers\Admin\DietPlanController::class)->only(['index', 'show']);
    });

    Route::middleware('permission:manage diet chart,admin')->group(function () {
        Route::resource('diet-plans', \App\Http\Controllers\Admin\DietPlanController::class)->except(['index', 'show']);
    });

    // -- Promotions --
    Route::middleware('permission:view promotions|manage promotions,admin')->group(function () {
        Route::resource('coupons', AdminCouponController::class)->only(['index', 'show']);
        Route::get('newsletter/export', [AdminNewsletterSubscriberController::class, 'export'])->name('newsletter.export');
        Route::resource('newsletter', AdminNewsletterSubscriberController::class)->only(['index', 'show']);
        Route::resource('contact-leads', AdminContactLeadController::class)->only(['index', 'show']);
    });

    Route::middleware('permission:manage promotions,admin')->group(function () {
        Route::resource('coupons', AdminCouponController::class)->except(['index', 'show']);
        Route::resource('newsletter', AdminNewsletterSubscriberController::class)->except(['index', 'show']);
        Route::resource('contact-leads', AdminContactLeadController::class)->except(['index', 'show']);
    });

    // -- Content Management --
    Route::middleware('permission:view content|manage content,admin')->group(function () {
        Route::resource('blog-categories', AdminBlogCategoryController::class)->only(['index', 'show']);
        Route::get('blog-posts-trash', [AdminBlogPostController::class, 'trash'])->name('blog-posts.trash');
        Route::resource('blog-posts', AdminBlogPostController::class)->only(['index', 'show']);
    });

    Route::middleware('permission:manage content,admin')->group(function () {
        Route::resource('blog-categories', AdminBlogCategoryController::class)->except(['index', 'show']);
        Route::delete('blog-posts-trash/bulk-force-delete', [AdminBlogPostController::class, 'bulkForceDestroy'])->name('blog-posts.bulk-force-destroy');
        Route::patch('blog-posts-trash/{blogPost}/restore', [AdminBlogPostController::class, 'restore'])->name('blog-posts.restore');
        Route::delete('blog-posts-trash/{blogPost}/force-delete', [AdminBlogPostController::class, 'forceDestroy'])->name('blog-posts.force-destroy');
        Route::post('blog-posts/bulk-destroy', [AdminBlogPostController::class, 'bulkForceDestroy'])->name('blog-posts.bulk-destroy');
        Route::post('blog-posts/upload-image', [AdminBlogPostController::class, 'uploadImage'])->name('blog-posts.upload-image');
        Route::resource('blog-posts', AdminBlogPostController::class)->except(['index', 'show']);
    });

    // -- Customers & Support --
    Route::middleware('permission:view customers|manage customers,admin')->group(function () {
        Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class)->only(['index', 'show']);
        Route::resource('support-tickets', AdminSupportTicketController::class)->only(['index', 'show']);
    });

    Route::middleware('permission:manage customers,admin')->group(function () {
        Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class)->except(['index', 'show']);
        Route::resource('support-tickets', AdminSupportTicketController::class)->except(['index', 'show']);
        Route::post('support-tickets/{support_ticket}/reply', [AdminSupportTicketController::class, 'reply'])->name('support-tickets.reply');
    });

    // -- Moderation --
    Route::middleware('permission:view moderation|manage moderation,admin')->group(function () {
        Route::resource('reviews', \App\Http\Controllers\Admin\ReviewController::class)->only(['index', 'show']);
        Route::resource('video-reviews', \App\Http\Controllers\Admin\VideoReviewController::class)->only(['index', 'show']);
    });

    Route::middleware('permission:manage moderation,admin')->group(function () {
        Route::resource('reviews', \App\Http\Controllers\Admin\ReviewController::class)->except(['index', 'show']);
        Route::resource('video-reviews', \App\Http\Controllers\Admin\VideoReviewController::class)->except(['index', 'show']);
    });

    // -- Orders & Invoices --
    Route::middleware('permission:view orders|manage orders,admin')->group(function () {
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::get('orders/{order}/invoice-download', [UserOrderController::class, 'invoiceDownload'])->name('orders.invoice-download');
        Route::resource('order-returns', \App\Http\Controllers\Admin\OrderReturnController::class)->only(['index', 'show']);
        Route::resource('invoices', \App\Http\Controllers\Admin\InvoiceController::class)->only(['index', 'show']);
    });

    Route::middleware('permission:manage orders,admin')->group(function () {
        Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::resource('order-returns', \App\Http\Controllers\Admin\OrderReturnController::class)->except(['index', 'show']);
        Route::resource('invoices', \App\Http\Controllers\Admin\InvoiceController::class)->except(['index', 'show']); // typically invoices aren't edited but preserving existing routes
    });

    // -- Loyalty --
    Route::prefix('loyalty')->name('loyalty.')->group(function () {
        Route::middleware('permission:view loyalty|manage loyalty,admin')->group(function () {
            Route::get('/settings', [\App\Http\Controllers\Admin\LoyaltyController::class, 'settings'])->name('settings');
            Route::get('/transactions', [\App\Http\Controllers\Admin\LoyaltyController::class, 'transactions'])->name('transactions');
        });

        Route::middleware('permission:manage loyalty,admin')->group(function () {
            Route::post('/settings', [\App\Http\Controllers\Admin\LoyaltyController::class, 'updateSettings'])->name('settings.update');
        });
    });

    // -- Administration (Notifications, Settings, Side Section) --
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::middleware('permission:view administration|manage administration,admin')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
        });
        Route::middleware('permission:manage administration,admin')->group(function () {
            Route::post('/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('read');
            Route::post('/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('read-all');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::middleware('permission:view administration|manage administration,admin')->group(function () {
            Route::get('/general', [\App\Http\Controllers\Admin\GeneralSettingController::class, 'index'])->name('general');
            Route::get('/payment-gateways', [\App\Http\Controllers\Admin\GeneralSettingController::class, 'paymentGateways'])->name('payment-gateways');
            Route::get('/auth', [\App\Http\Controllers\Admin\GeneralSettingController::class, 'authSettings'])->name('auth');
        });
        Route::middleware('permission:manage administration,admin')->group(function () {
            Route::post('/general', [\App\Http\Controllers\Admin\GeneralSettingController::class, 'update'])->name('general.update');
            Route::post('/payment-gateways', [\App\Http\Controllers\Admin\GeneralSettingController::class, 'updatePaymentGateways'])->name('payment-gateways.update');
            Route::post('/auth', [\App\Http\Controllers\Admin\GeneralSettingController::class, 'updateAuthSettings'])->name('auth.update');
        });
    });

    Route::middleware('permission:view administration|manage administration,admin')->group(function () {
        Route::get('side-section', [\App\Http\Controllers\Admin\SideSectionController::class, 'index'])->name('side-section.index');
    });
    Route::middleware('permission:manage administration,admin')->group(function () {
        Route::post('side-section', [\App\Http\Controllers\Admin\SideSectionController::class, 'update'])->name('side-section.update');
    });
});

// ── Assessment / Quiz Management ─────────────────────────────────────────────
Route::prefix('admin/assessment')->name('admin.assessment.')->middleware('auth:admin')->group(function () {
    Route::middleware('permission:view assessment|manage assessment,admin')->group(function () {
        Route::resource('questions', \App\Http\Controllers\Admin\AssessmentQuestionController::class)->only(['index', 'show']);
    });
    
    Route::middleware('permission:manage assessment,admin')->group(function () {
        Route::resource('questions', \App\Http\Controllers\Admin\AssessmentQuestionController::class)->except(['index', 'show']);
        Route::patch('questions/{question}/toggle', [\App\Http\Controllers\Admin\AssessmentQuestionController::class, 'toggleActive'])->name('questions.toggle');
        Route::post('questions/reorder', [\App\Http\Controllers\Admin\AssessmentQuestionController::class, 'reorder'])->name('questions.reorder');
        Route::patch('sections/rename', [\App\Http\Controllers\Admin\AssessmentQuestionController::class, 'renameSection'])->name('sections.rename');
    });
});

// ── Pediatric Nutrition Management (Diet Chart) ────────────────────────────────
Route::prefix('admin/nutrition')->name('admin.nutrition.')->middleware('auth:admin')->group(function () {
    Route::middleware('permission:view diet chart|manage diet chart,admin')->group(function () {
        Route::resource('questions', \App\Http\Controllers\Admin\Nutrition\QuestionnaireAdminController::class)->only(['index', 'show']);
        Route::resource('diet-subscriptions', \App\Http\Controllers\Admin\DietSubscriptionController::class)->only(['index', 'show']);
        Route::get('foods/data', [\App\Http\Controllers\Admin\Nutrition\FoodController::class, 'data'])->name('foods.data');
        Route::get('foods', [\App\Http\Controllers\Admin\Nutrition\FoodController::class, 'index'])->name('foods.index');
    });
    
    Route::middleware('permission:manage diet chart,admin')->group(function () {
        Route::resource('questions', \App\Http\Controllers\Admin\Nutrition\QuestionnaireAdminController::class)->except(['index', 'show']);
        Route::resource('diet-subscriptions', \App\Http\Controllers\Admin\DietSubscriptionController::class)->except(['index', 'show']);
        Route::get('leads', [\App\Http\Controllers\Admin\NutritionLeadController::class, 'index'])->name('leads.index');
        Route::patch('leads/{lead}/assign', [\App\Http\Controllers\Admin\NutritionLeadController::class, 'assign'])->name('leads.assign');
        Route::patch('leads/bulk-assign', [\App\Http\Controllers\Admin\NutritionLeadController::class, 'bulkAssign'])->name('leads.bulk-assign');

        Route::get('my-leads', [\App\Http\Controllers\Admin\NutritionistLeadController::class, 'index'])->name('lead-queue.index');
        Route::get('my-leads/{lead}', [\App\Http\Controllers\Admin\NutritionistLeadController::class, 'show'])->name('lead-queue.show');
        Route::post('my-leads/{lead}/generate', [\App\Http\Controllers\Admin\NutritionistLeadController::class, 'generate'])->name('lead-queue.generate');
        Route::patch('my-leads/{lead}/diet-plans/{dietPlan}', [\App\Http\Controllers\Admin\NutritionistLeadController::class, 'update'])->name('lead-queue.update');
        Route::patch('my-leads/{lead}/diet-plans/{dietPlan}/products', [\App\Http\Controllers\Admin\NutritionistLeadController::class, 'updateProducts'])->name('lead-queue.products');
        Route::patch('my-leads/{lead}/diet-plans/{dietPlan}/approve', [\App\Http\Controllers\Admin\NutritionistLeadController::class, 'approve'])->name('lead-queue.approve');
    });
});
