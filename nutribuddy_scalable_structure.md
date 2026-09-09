# 🏗️ Nutribuddy — Scalable Module-Based Structure Guide

## 📊 Current State vs Recommended State

### ❌ Current Problem (What You Have Now)
```
app/
├── Http/Controllers/
│   ├── Admin/         ← 34 files dumped here!
│   ├── Frontend/      ← 28 files dumped here!
│   └── Api/
├── Models/            ← 39 models all flat!
├── Services/          ← Mixed services
└── ...
```
**Problems:**
- `ProductController.php` = **59,806 bytes** (WAY too big!)
- `DashboardController.php` = **45,029 bytes** (also massive!)
- 39 models all in one flat folder
- No clear domain ownership
- Hard to find things, hard to scale, hard to team-work

---

## ✅ Recommended: Domain Module Structure

```
app/
├── Modules/
│   ├── Auth/
│   ├── Product/
│   ├── Order/
│   ├── Cart/
│   ├── Payment/
│   ├── Blog/
│   ├── Assessment/
│   ├── Nutrition/
│   ├── Coupon/
│   ├── Support/
│   ├── Inventory/
│   ├── User/
│   ├── Admin/
│   └── Notification/
├── Http/Controllers/    ← thin controllers only (entry points)
├── Models/              ← keep or move into Modules
└── Services/            ← global/shared services only
```

---

## 📦 How Many Modules? — 13 Modules

| # | Module | What it handles |
|---|--------|----------------|
| 1 | **Auth** | Login, Register, Google OAuth, Guards |
| 2 | **Product** | Products, Variants, Attributes, Categories, Reviews, Images |
| 3 | **Order** | Orders, Order Items, Status History, Returns |
| 4 | **Cart** | Cart, Cart Items, Session management |
| 5 | **Payment** | Razorpay, Cashfree, Diet Payment, Coin/Wallet |
| 6 | **Blog** | Blog Posts, Blog Categories |
| 7 | **Assessment** | Questions, Options, Attempts, Answers, Scores |
| 8 | **Nutrition** | Diet Plans, Diet Subscriptions, Ingredients, Meal Plans |
| 9 | **Coupon** | Coupons, Coupon Usage, User Coupon |
| 10 | **Support** | Support Tickets, Messages |
| 11 | **Inventory** | Stock, Inventory tracking |
| 12 | **User** | Profile, Address, Wallet, Loyalty, Newsletter |
| 13 | **Admin** | Dashboard, Settings, Roles, Permissions |

---

## 🗂️ Full Folder Structure Per Module

Each module follows this pattern:
```
app/Modules/Product/
├── Controllers/
│   ├── Admin/
│   │   ├── ProductController.php
│   │   ├── ProductVariantController.php
│   │   └── AttributeController.php
│   └── Frontend/
│       ├── ProductController.php
│       └── ProductReviewController.php
├── Models/
│   ├── Product.php
│   ├── ProductVariant.php
│   ├── ProductImage.php
│   ├── ProductReview.php
│   └── Attribute.php
├── Services/
│   ├── ProductService.php
│   └── ProductImageService.php
├── Requests/
│   ├── StoreProductRequest.php
│   └── UpdateProductRequest.php
├── Routes/
│   ├── admin.php
│   └── web.php
└── ProductServiceProvider.php
```

---

## 📁 Complete New Structure (All 13 Modules)

```
app/
├── Modules/
│   │
│   ├── Auth/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   └── GoogleAuthController.php
│   │   ├── Models/ (uses User model from User module)
│   │   ├── Services/AuthService.php
│   │   ├── Requests/LoginRequest.php
│   │   └── Routes/auth.php
│   │
│   ├── Product/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── ProductController.php       ← split big controller
│   │   │   │   ├── ProductVariantController.php
│   │   │   │   ├── AttributeController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   └── ReviewController.php
│   │   │   └── Frontend/
│   │   │       ├── ProductController.php
│   │   │       └── ProductReviewController.php
│   │   ├── Models/
│   │   │   ├── Product.php
│   │   │   ├── ProductVariant.php
│   │   │   ├── ProductImage.php
│   │   │   ├── ProductReview.php
│   │   │   ├── Attribute.php
│   │   │   └── Category.php
│   │   ├── Services/
│   │   │   ├── ProductService.php
│   │   │   ├── ProductImageService.php
│   │   │   └── VariantService.php
│   │   ├── Requests/
│   │   │   ├── StoreProductRequest.php
│   │   │   └── UpdateProductRequest.php
│   │   └── Routes/
│   │       ├── admin.php
│   │       └── web.php
│   │
│   ├── Order/
│   │   ├── Controllers/
│   │   │   ├── Admin/OrderController.php
│   │   │   ├── Admin/OrderReturnController.php
│   │   │   ├── Admin/InvoiceController.php
│   │   │   └── Frontend/UserOrderController.php
│   │   │   └── Frontend/UserReturnController.php
│   │   ├── Models/
│   │   │   ├── Order.php
│   │   │   ├── OrderItem.php
│   │   │   ├── OrderReturn.php
│   │   │   ├── OrderReturnItem.php
│   │   │   └── OrderStatusHistory.php
│   │   ├── Services/
│   │   │   ├── OrderService.php
│   │   │   └── ReturnService.php
│   │   └── Routes/
│   │       ├── admin.php
│   │       └── web.php
│   │
│   ├── Cart/
│   │   ├── Controllers/Frontend/CartController.php
│   │   ├── Controllers/Frontend/CheckoutController.php
│   │   ├── Models/
│   │   │   ├── Cart.php
│   │   │   └── CartItem.php
│   │   ├── Services/
│   │   │   ├── CartService.php
│   │   │   └── CheckoutService.php
│   │   └── Routes/web.php
│   │
│   ├── Payment/
│   │   ├── Controllers/
│   │   │   ├── RazorpayPaymentController.php
│   │   │   ├── CashfreePaymentController.php
│   │   │   └── DietPaymentController.php
│   │   ├── Models/Payment.php
│   │   ├── Services/
│   │   │   ├── RazorpayService.php
│   │   │   ├── CashfreeService.php
│   │   │   └── PaymentService.php
│   │   └── Routes/web.php
│   │
│   ├── Blog/
│   │   ├── Controllers/
│   │   │   ├── Admin/BlogPostController.php
│   │   │   ├── Admin/BlogCategoryController.php
│   │   │   └── Frontend/BlogController.php
│   │   ├── Models/
│   │   │   ├── BlogPost.php
│   │   │   └── BlogCategory.php
│   │   └── Routes/
│   │       ├── admin.php
│   │       └── web.php
│   │
│   ├── Assessment/
│   │   ├── Controllers/
│   │   │   ├── Admin/AssessmentQuestionController.php
│   │   │   └── Frontend/AssessmentController.php
│   │   ├── Models/
│   │   │   ├── AssessmentQuestion.php
│   │   │   ├── AssessmentOption.php
│   │   │   ├── AssessmentAttempt.php
│   │   │   ├── AssessmentAnswer.php
│   │   │   └── AssessmentSectionScore.php
│   │   ├── Services/AssessmentService.php
│   │   └── Routes/
│   │       ├── admin.php
│   │       └── web.php
│   │
│   ├── Nutrition/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── IngredientController.php
│   │   │   │   ├── IngredientCategoryController.php
│   │   │   │   ├── DietPlanController.php
│   │   │   │   └── DietSubscriptionController.php
│   │   │   └── Frontend/
│   │   │       └── Nutrition/ (existing)
│   │   ├── Models/
│   │   │   ├── Ingredient.php
│   │   │   ├── IngredientBenefit.php
│   │   │   ├── IngredientCategory.php
│   │   │   └── DietSubscription.php
│   │   ├── Services/
│   │   │   └── NutritionService.php
│   │   └── Routes/nutrition.php
│   │
│   ├── Coupon/
│   │   ├── Controllers/
│   │   │   ├── Admin/CouponController.php
│   │   │   └── Frontend/UserCouponController.php
│   │   ├── Models/
│   │   │   ├── Coupon.php
│   │   │   └── CouponUsage.php
│   │   ├── Services/CouponService.php
│   │   └── Routes/
│   │       ├── admin.php
│   │       └── web.php
│   │
│   ├── Support/
│   │   ├── Controllers/
│   │   │   ├── Admin/SupportTicketController.php
│   │   │   └── Frontend/UserSupportTicketController.php
│   │   ├── Models/
│   │   │   ├── SupportTicket.php
│   │   │   └── SupportTicketMessage.php
│   │   └── Routes/
│   │       ├── admin.php
│   │       └── web.php
│   │
│   ├── Inventory/
│   │   ├── Controllers/Admin/InventoryController.php
│   │   ├── Models/
│   │   │   ├── Inventory.php
│   │   │   └── TaxRate.php
│   │   ├── Services/
│   │   │   ├── StockService.php
│   │   │   └── PricingService.php
│   │   └── Routes/admin.php
│   │
│   ├── User/
│   │   ├── Controllers/
│   │   │   ├── Admin/CustomerController.php
│   │   │   └── Frontend/
│   │   │       ├── UserProfileController.php
│   │   │       ├── UserAddressController.php
│   │   │       ├── UserWalletController.php
│   │   │       └── ChildProfileController.php
│   │   ├── Models/
│   │   │   ├── User.php
│   │   │   ├── UserAddress.php
│   │   │   ├── CustomerAddress.php
│   │   │   ├── CoinTransaction.php
│   │   │   └── NewsletterSubscriber.php
│   │   ├── Services/
│   │   │   └── UserService.php
│   │   └── Routes/
│   │       ├── admin.php
│   │       └── web.php
│   │
│   └── Admin/
│       ├── Controllers/
│       │   ├── DashboardController.php  ← split into widgets
│       │   ├── GeneralSettingController.php
│       │   ├── RoleController.php
│       │   ├── NotificationController.php
│       │   ├── UserController.php
│       │   └── ContactLeadController.php
│       ├── Services/
│       │   └── DashboardService.php    ← move heavy logic here
│       └── Routes/admin.php
│
├── Http/
│   ├── Controllers/
│   │   └── Controller.php             ← base only
│   ├── Middleware/
│   └── Requests/                      ← global requests only
│
├── Services/                          ← shared/global services only
│   └── Notifications/
│
└── Providers/
    ├── AppServiceProvider.php
    └── ModuleServiceProvider.php      ← loads all module routes
```

---

## 🔑 Key Rules for Each Module

### 1. Controller = Thin (max 30 lines per method)
```php
// ❌ BAD — controller doing everything
public function store(Request $request) {
    // validate... process image... update stock... send email... 200 lines
}

// ✅ GOOD — controller delegates to service
public function store(StoreProductRequest $request) {
    $product = $this->productService->create($request->validated());
    return redirect()->route('admin.products.index');
}
```

### 2. Service = Business Logic
```php
// app/Modules/Product/Services/ProductService.php
class ProductService {
    public function create(array $data): Product { ... }
    public function updateVariants(Product $p, array $variants): void { ... }
}
```

### 3. Request = Validation
```php
// app/Modules/Product/Requests/StoreProductRequest.php
class StoreProductRequest extends FormRequest {
    public function rules(): array { return [...]; }
}
```

### 4. Each Module has its own Routes file
```php
// app/Modules/Product/Routes/admin.php
Route::prefix('products')->group(function() {
    Route::resource('/', ProductController::class);
    Route::resource('variants', ProductVariantController::class);
});
```

---

## 🚀 Priority: What to Fix FIRST

| Priority | Action | Why |
|----------|--------|-----|
| 🔴 **1st** | Split `ProductController.php` (60KB!) | Too big, unmaintainable |
| 🔴 **2nd** | Split `DashboardController.php` (45KB!) | Same issue |
| 🟠 **3rd** | Group Models into Module subfolders | 39 flat files = chaos |
| 🟡 **4th** | Create Module-level `Routes/` files | Replace mega route files |
| 🟢 **5th** | Add `ModuleServiceProvider.php` | Auto-register all modules |

---

## 📌 Summary

- **13 total modules** covering all domains
- Each module is **self-contained**: Controller + Model + Service + Request + Routes
- Controllers stay **thin** — all logic goes to **Services**
- Routes are **split per module** instead of one big `admin.php`
- This makes it easy to **add new developers**, **add new features**, and **find bugs fast**
