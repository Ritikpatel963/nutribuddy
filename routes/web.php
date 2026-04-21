<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\AiapplicationController;
use App\Http\Controllers\Frontend\ChartController;
use App\Http\Controllers\Frontend\ComponentpageController;
use App\Http\Controllers\Frontend\FormsController;
use App\Http\Controllers\Frontend\TableController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Admin\AuthenticationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\RoleandaccessController;
use App\Http\Controllers\Admin\CryptocurrencyController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ContactLeadController as AdminContactLeadController;
use App\Http\Controllers\Admin\BlogCategoryController as AdminBlogCategoryController;
use App\Http\Controllers\Admin\BlogPostController as AdminBlogPostController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\NewsletterSubscriberController as AdminNewsletterSubscriberController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductVariantController as AdminProductVariantController;
use App\Http\Controllers\Admin\IngredientController as AdminIngredientController;
use App\Http\Controllers\Admin\IngredientCategoryController as AdminIngredientCategoryController;
use App\Http\Controllers\Admin\SupportTicketController as AdminSupportTicketController;
use App\Http\Controllers\Admin\TaxRateController as AdminTaxRateController;
use App\Models\Ingredient;
use App\Models\IngredientCategory;


Route::middleware('auth')->controller(DashboardController::class)->group(function () {
    Route::get('/admin', 'index')->name('admin.index');
});

Route::controller(HomeController::class)->group(function () {
    Route::get('calendar', 'calendar')->name('calendar');
    Route::get('chatmessage', 'chatMessage')->name('chatMessage');
    Route::get('chatempty', 'chatempty')->name('chatempty');
    Route::get('email', 'email')->name('email');
    Route::get('error', 'error1')->name('error');
    Route::get('faq', 'faq')->name('faq');
    Route::get('gallery', 'gallery')->name('gallery');
    Route::get('kanban', 'kanban')->name('kanban');
    Route::get('pricing', 'pricing')->name('pricing');
    Route::get('termscondition', 'termsCondition')->name('termsCondition');
    Route::get('widgets', 'widgets')->name('widgets');
    Route::get('chatprofile', 'chatProfile')->name('chatProfile');
    Route::get('veiwdetails', 'veiwDetails')->name('veiwDetails');
    Route::get('blankPage', 'blankPage')->name('blankPage');
    Route::get('comingSoon', 'comingSoon')->name('comingSoon');
    Route::get('maintenance', 'maintenance')->name('maintenance');
    Route::get('starred', 'starred')->name('starred');
    Route::get('testimonials', 'testimonials')->name('testimonials');

});

// aiApplication
Route::prefix('aiapplication')->group(function () {
    Route::controller(AiapplicationController::class)->group(function () {
        Route::get('/codegenerator', 'codeGenerator')->name('codeGenerator');
        Route::get('/codegeneratornew', 'codeGeneratorNew')->name('codeGeneratorNew');
        Route::get('/imagegenerator', 'imageGenerator')->name('imageGenerator');
        Route::get('/textgeneratornew', 'textGeneratorNew')->name('textGeneratorNew');
        Route::get('/textgenerator', 'textGenerator')->name('textGenerator');
        Route::get('/videogenerator', 'videoGenerator')->name('videoGenerator');
        Route::get('/voicegenerator', 'voiceGenerator')->name('voiceGenerator');
    });
});

// Admin Authentication
Route::prefix('authentication')->group(function () {
    Route::controller(AuthenticationController::class)->group(function () {
        Route::get('/forgotpassword', 'forgotPassword')->name('forgotPassword');
        Route::get('/signin', 'signin')->name('signin');
        Route::post('/login', 'login')->name('admin.login.post');
        Route::post('/logout', 'logout')->name('admin.logout');
        Route::get('/signup', 'signup')->name('signup');
    });
});

// User (Frontend) Authentication
Route::name('frontend.')->group(function () {
    Route::controller(\App\Http\Controllers\Frontend\AuthController::class)->group(function () {
        Route::get('/login', 'showLogin')->name('login');
        Route::post('/send-otp', 'sendOtp')->name('sendOtp');
        Route::post('/verify-otp', 'verifyOtp')->name('verifyOtp');
        Route::post('/logout', 'logout')->name('logout');
    });
});

// chart
Route::prefix('chart')->group(function () {
    Route::controller(ChartController::class)->group(function () {
        Route::get('/columnchart', 'columnChart')->name('columnChart');
        Route::get('/linechart', 'lineChart')->name('lineChart');
        Route::get('/piechart', 'pieChart')->name('pieChart');
    });
});

// Componentpage
Route::prefix('componentspage')->group(function () {
    Route::controller(ComponentpageController::class)->group(function () {
        Route::get('/alert', 'alert')->name('alert');
        Route::get('/avatar', 'avatar')->name('avatar');
        Route::get('/badges', 'badges')->name('badges');
        Route::get('/button', 'button')->name('button');
        Route::get('/calendar', 'calendar')->name('calendar');
        Route::get('/card', 'card')->name('card');
        Route::get('/carousel', 'carousel')->name('carousel');
        Route::get('/colors', 'colors')->name('colors');
        Route::get('/dropdown', 'dropdown')->name('dropdown');
        Route::get('/imageupload', 'imageUpload')->name('imageUpload');
        Route::get('/list', 'list')->name('list');
        Route::get('/pagination', 'pagination')->name('pagination');
        Route::get('/progress', 'progress')->name('progress');
        Route::get('/radio', 'radio')->name('radio');
        Route::get('/starrating', 'starRating')->name('starRating');
        Route::get('/switch', 'switch')->name('switch');
        Route::get('/tabs', 'tabs')->name('tabs');
        Route::get('/tags', 'tags')->name('tags');
        Route::get('/tooltip', 'tooltip')->name('tooltip');
        Route::get('/typography', 'typography')->name('typography');
        Route::get('/videos', 'videos')->name('videos');
    });
});

// Dashboard
Route::prefix('dashboard')->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::get('/index2', 'index2')->name('index2');
        Route::get('/index3', 'index3')->name('index3');
        Route::get('/index4', 'index4')->name('index4');
        Route::get('/index5', 'index5')->name('index5');
        Route::get('/index6', 'index6')->name('index6');
        Route::get('/index7', 'index7')->name('index7');
        Route::get('/index8', 'index8')->name('index8');
        Route::get('/index9', 'index9')->name('index9');
        Route::get('/index10', 'index10')->name('index10');
        Route::get('/wallet', 'wallet')->name('wallet');
    });
});

// Forms
Route::prefix('forms')->group(function () {
    Route::controller(FormsController::class)->group(function () {
        Route::get('/form-layout', 'formLayout')->name('formLayout');
        Route::get('/form-validation', 'formValidation')->name('formValidation');
        Route::get('/form', 'form')->name('form');
        Route::get('/wizard', 'wizard')->name('wizard');
    });
});

// invoice/invoiceList
Route::prefix('invoice')->group(function () {
    Route::controller(InvoiceController::class)->group(function () {
        Route::get('/invoice-add', 'invoiceAdd')->name('invoiceAdd');
        Route::get('/invoice-edit', 'invoiceEdit')->name('invoiceEdit');
        Route::get('/invoice-list', 'invoiceList')->name('invoiceList');
        Route::get('/invoice-preview', 'invoicePreview')->name('invoicePreview');
    });
});

// Settings
Route::prefix('settings')->group(function () {
    Route::controller(SettingsController::class)->group(function () {
        Route::get('/company', 'company')->name('company');
        Route::get('/currencies', 'currencies')->name('currencies');
        Route::get('/language', 'language')->name('language');
        Route::get('/notification', 'notification')->name('notification');
        Route::get('/notification-alert', 'notificationAlert')->name('notificationAlert');
        Route::get('/payment-gateway', 'paymentGateway')->name('paymentGateway');
        Route::get('/theme', 'theme')->name('theme');
    });
});

// Table
Route::prefix('table')->group(function () {
    Route::controller(TableController::class)->group(function () {
        Route::get('/tablebasic', 'tableBasic')->name('tableBasic');
        Route::get('/tabledata', 'tableData')->name('tableData');
    });
});

// Users
Route::prefix('users')->group(function () {
    Route::controller(UsersController::class)->group(function () {
        Route::get('/add-user', 'addUser')->name('addUser');
        Route::get('/users-grid', 'usersGrid')->name('usersGrid');
        Route::get('/users-list', 'usersList')->name('usersList');
        Route::get('/view-profile', 'viewProfile')->name('viewProfile');
    });
});

// Users
Route::prefix('blog')->group(function () {
    Route::controller(BlogController::class)->group(function () {
        Route::get('/addBlog', 'addBlog')->name('addBlog');
        Route::get('/blog', 'blog')->name('blog');
        Route::get('/blogDetails', 'blogDetails')->name('blogDetails');
    });
});

// Users
Route::prefix('roleandaccess')->group(function () {
    Route::controller(RoleandaccessController::class)->group(function () {
        Route::get('/assignRole', 'assignRole')->name('assignRole');
        Route::get('/roleAaccess', 'roleAaccess')->name('roleAaccess');
    });
});

// Users
Route::prefix('cryptocurrency')->group(function () {
    Route::controller(CryptocurrencyController::class)->group(function () {
        Route::get('/marketplace', 'marketplace')->name('marketplace');
        Route::get('/marketplacedetails', 'marketplaceDetails')->name('marketplaceDetails');
        Route::get('/portfolio', 'portfolio')->name('portfolio');
        Route::get('/wallet', 'wallet')->name('wallet');
    });
});

Route::prefix('admin/ecommerce')->name('admin.ecommerce.')->middleware('auth')->group(function () {
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
    Route::resource('invoices', \App\Http\Controllers\Admin\InvoiceController::class)->only(['show']);

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

    // Side Section Route
    Route::get('side-section', [\App\Http\Controllers\Admin\SideSectionController::class, 'index'])->name('side-section.index');
    Route::post('side-section', [\App\Http\Controllers\Admin\SideSectionController::class, 'update'])->name('side-section.update');
});


// Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'pages.about-us')->name('about');
Route::get('/product', function () {
    $resolveIngredientMeta = function (?IngredientCategory $category): array {
        $normalized = strtolower(trim(($category?->slug ?? '') . ' ' . ($category?->name ?? '')));
        $filterKey = match (true) {
            str_contains($normalized, 'ayur') => 'ay',
            str_contains($normalized, 'vit') => 'vi',
            str_contains($normalized, 'min') => 'mi',
            str_contains($normalized, 'extract') => 'ex',
            str_contains($normalized, 'base') => 'ba',
            default => substr(preg_replace('/[^a-z]/', '', strtolower($category?->slug ?: $category?->name ?: 'other')), 0, 2) ?: 'ot',
        };

        $meta = match ($filterKey) {
            'ay' => ['dot_color' => '#00D68F', 'badge_bg' => 'rgba(0,214,143,.12)', 'emoji_bg' => 'rgba(0,214,143,.1)'],
            'vi' => ['dot_color' => '#00BFFF', 'badge_bg' => 'rgba(0,191,255,.12)', 'emoji_bg' => 'rgba(0,191,255,.1)'],
            'mi' => ['dot_color' => '#FFD600', 'badge_bg' => 'rgba(255,214,0,.12)', 'emoji_bg' => 'rgba(255,214,0,.1)'],
            'ex' => ['dot_color' => '#FF6B35', 'badge_bg' => 'rgba(255,107,53,.12)', 'emoji_bg' => 'rgba(255,107,53,.1)'],
            'ba' => ['dot_color' => 'rgba(255,255,255,.4)', 'badge_bg' => 'rgba(255,255,255,.06)', 'emoji_bg' => 'rgba(255,255,255,.05)'],
            default => ['dot_color' => '#7C3AED', 'badge_bg' => 'rgba(124,58,237,.12)', 'emoji_bg' => 'rgba(124,58,237,.1)'],
        };

        return [
            'key' => $filterKey,
            'name' => $category?->name ?: 'Other',
            'dot_color' => $meta['dot_color'],
            'badge_bg' => $meta['badge_bg'],
            'emoji_bg' => $meta['emoji_bg'],
        ];
    };

    $categories = IngredientCategory::where('is_active', true)
        ->withCount(['ingredients as ingredients_count' => fn($query) => $query->where('is_active', true)])
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

    $ingredientCategories = $categories
        ->map(function (IngredientCategory $category) use ($resolveIngredientMeta) {
            $meta = $resolveIngredientMeta($category);

            return [
                'key' => $meta['key'],
                'name' => $meta['name'],
                'count' => (int) $category->ingredients_count,
                'dot_color' => $meta['dot_color'],
            ];
        })
        ->values();

    $ingredients = Ingredient::with(['category', 'benefits'])
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get()
        ->map(function (Ingredient $ingredient) use ($resolveIngredientMeta) {
            $meta = $resolveIngredientMeta($ingredient->category);
            $name = trim((string) ($ingredient->main_heading ?: $ingredient->short_heading ?: 'Ingredient'));
            $shortName = trim((string) ($ingredient->short_heading ?: $ingredient->main_heading ?: $name));
            $benefits = $ingredient->benefits
                ->pluck('heading')
                ->filter(fn($heading) => filled($heading))
                ->values()
                ->all();

            return [
                'id' => 'ingredient-' . $ingredient->id,
                'name' => $name,
                'shortName' => $shortName,
                'emoji' => '',
                'cat' => $meta['key'],
                'catLabel' => $meta['name'],
                'latin' => $shortName,
                'desc' => (string) ($ingredient->description ?: ''),
                'benefits' => $benefits,
                'dosage' => trim(collect([$ingredient->dosage_heading_one, $ingredient->dosage_heading_two])->filter()->implode(' • ')) ?: 'Details available in ingredient panel',
                'badgeColor' => $meta['dot_color'],
                'badgeBg' => $meta['badge_bg'],
                'emojiBg' => $meta['emoji_bg'],
                'image' => $ingredient->icon_path ? asset('storage/' . $ingredient->icon_path) : null,
            ];
        })
        ->values();

    $ayurvedicCount = (int) data_get($ingredientCategories->firstWhere('key', 'ay'), 'count', 0);
    $vitaminCount = (int) data_get($ingredientCategories->firstWhere('key', 'vi'), 'count', 0);
    $mineralCount = (int) data_get($ingredientCategories->firstWhere('key', 'mi'), 'count', 0);

    $summaryStats = [
        ['value' => $ingredients->count(), 'label' => 'Total Ingredients', 'color' => '#FF4D8F'],
        ['value' => $ayurvedicCount, 'label' => 'Ayurvedic Herbs', 'color' => '#00D68F'],
        ['value' => $vitaminCount, 'label' => 'Vitamins', 'color' => '#00BFFF'],
        ['value' => $mineralCount, 'label' => 'Minerals', 'color' => '#FFD600'],
        ['value' => 0, 'label' => 'Artificial Colors', 'color' => '#FF6B35'],
        ['value' => 0, 'label' => 'Gelatin / Animal', 'color' => '#7C3AED'],
    ];

    return view('pages.product', [
        'ingredientCategoryFilters' => $ingredientCategories,
        'ingredientTotalCount' => $ingredients->count(),
        'ingredientItems' => $ingredients,
        'ingredientSummaryStats' => $summaryStats,
    ]);
})->name('product');
Route::get('/product', [\App\Http\Controllers\Frontend\ProductController::class, 'index'])->name('product');
Route::get('/product/{slug}', [\App\Http\Controllers\Frontend\ProductController::class, 'show'])->name('product.show');
Route::view('/diet-chart', 'pages.diet-chart')->name('diet_chart');
Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/checkout', 'pages.checkout')->name('checkout');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/return-policy', 'pages.return-policy')->name('return-policy');
Route::view('/terms', 'pages.terms')->name('terms');
Route::view('/user-return', 'pages.user-panel.user-return')->name('user-return');

Route::view('/userdashboard', 'pages.user-panel.userdashboard')->name('userdashboard');
Route::view('/meal-plan', 'pages.user-panel.meal-plan')->name('meal-plan');
Route::view('/health-scores', 'pages.user-panel.health-scores')->name('health-scores');
Route::view('/supplement', 'pages.user-panel.supplement')->name('supplement');
Route::view('/child-profile', 'pages.user-panel.child-profile')->name('child-profile');
Route::view('/growth-signal', 'pages.user-panel.growth-signal')->name('growth-signal');
Route::view('/check-in', 'pages.user-panel.check-in')->name('check-in');
Route::middleware('auth')->group(function () {
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
});
