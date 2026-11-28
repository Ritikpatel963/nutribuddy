<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\TaxRate;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ProductSectionRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_product_rating_keeps_review_count_on_the_same_row(): void
    {
        $productCss = file_get_contents(public_path('assets/css/product.css'));

        $this->assertMatchesRegularExpression(
            '/\.pdp-hero \.pdp-info \.pdp-rating \{[^}]*flex-direction: row;[^}]*\}/s',
            $productCss
        );
        $this->assertMatchesRegularExpression(
            '/\.pdp-hero \.pdp-info \.pdp-rating \.rating-count \{[^}]*flex-basis: auto;[^}]*white-space: nowrap;[^}]*\}/s',
            $productCss
        );
    }

    public function test_delivery_estimate_icon_aligns_with_its_text(): void
    {
        $productCss = file_get_contents(public_path('assets/css/product.css'));
        $productView = file_get_contents(resource_path('views/frontend/storefront/products/show.blade.php'));

        $this->assertMatchesRegularExpression(
            '/\.pdp-hero \.pdp-info \.pdp-delivery-estimate \{[^}]*display: flex;[^}]*align-items: center;[^}]*\}/s',
            $productCss
        );
        $this->assertStringContainsString(
            '<span>Estimated delivery between <b>17 November – 19 November.</b></span>',
            $productView
        );
    }

    public function test_world_nutrition_map_omits_country_picker_and_research_notes(): void
    {
        $mapHtml = file_get_contents(public_path('assets/research/world-nutrition-map.html'));

        $this->assertStringNotContainsString('EXPLORE A COUNTRY', $mapHtml);
        $this->assertStringNotContainsString('class="nb-study-note"', $mapHtml);
        $this->assertStringNotContainsString('<details class="nb-research">', $mapHtml);
        $this->assertStringContainsString(".on('click', (_, d) => select(d.properties.id))", $mapHtml);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->app[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function test_storefront_product_catalog_page_renders_successfully(): void
    {
        $category = Category::factory()->create(['name' => 'Vitamins', 'is_active' => true]);
        $taxRate = TaxRate::factory()->create(['rate' => 18.0, 'is_active' => true]);

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'tax_rate_id' => $taxRate->id,
            'is_active' => true,
            'base_price' => 499.00,
        ]);

        $response = $this->get(route('product'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.all-products');
        $response->assertViewHas('products');
        $response->assertViewHas('categoryCounts');
    }

    public function test_product_detail_shows_suitable_age_after_size_when_age_is_not_configured(): void
    {
        $category = Category::factory()->create(['is_active' => true]);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Kids Lemon Gummies',
            'slug' => 'kids-lemon-gummies',
            'is_active' => true,
            'product_type' => 'variable',
            'is_variant_enabled' => true,
            'age_group' => null,
            'variant_types' => [
                'Flavour' => ['Lemon Ginger'],
                'Size' => ['15pcs'],
            ],
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'name' => '15 Piece Pack',
            'sku' => 'KIDS-GUMMY-15',
            'price' => 399.00,
            'attributes' => [
                'Flavour' => 'Lemon Ginger',
                'Size' => '15pcs',
            ],
            'is_active' => true,
            'is_default' => true,
        ]);

        $response = $this->get(route('product.show', 'kids-lemon-gummies'));

        $response->assertStatus(200);
        $response->assertSeeInOrder([
            'data-attribute-group="Size"',
            'pdp-suitable-age',
            'Suitable Age',
            '4+',
        ]);
        $response->assertDontSee('Choose Your Option');
        $response->assertDontSee('Choose the flavour, size, and suitable age before adding to cart.');
    }

    public function test_product_detail_page_loads_with_variants_and_inventory(): void
    {
        $category = Category::factory()->create(['is_active' => true]);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Brain Booster Gummies',
            'slug' => 'brain-booster-gummies',
            'is_active' => true,
            'product_type' => 'variable',
            'is_variant_enabled' => true,
            'variant_types' => [
                'Flavour' => ['Lemon Ginger', 'Orange'],
                'Size' => ['15pcs', '30pcs'],
                'Age Group' => [],
            ],
            'problem_subtitle' => 'Algal DHA, Brahmi & Shankhpushpi, with essential nutrients.',
            'coins_reward' => 91,
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'img/newwkadha.png',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => '60 Gummies Pack',
            'sku' => 'GUMMY-60',
            'price' => 649.00,
            'compare_at_price' => 699.00,
            'attributes' => [
                'Flavour' => 'Lemon Ginger',
                'Size' => '15pcs',
            ],
            'is_active' => true,
            'is_default' => true,
        ]);

        $secondVariant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => '30 Gummies Pack',
            'sku' => 'GUMMY-30',
            'price' => 399.00,
            'compare_at_price' => 449.00,
            'attributes' => [
                'Flavour' => 'Orange',
                'Size' => '30pcs',
            ],
            'is_active' => true,
            'is_default' => false,
        ]);

        Inventory::create([
            'product_id' => $product->id,
            'product_variant_id' => $secondVariant->id,
            'stock_qty' => 20,
            'track_stock' => true,
            'is_in_stock' => true,
        ]);

        Inventory::create([
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'stock_qty' => 50,
            'track_stock' => true,
            'is_in_stock' => true,
        ]);

        $ingredient = Ingredient::create([
            'main_heading' => 'Algal DHA',
            'short_heading' => 'Algal DHA',
            'description' => 'Plant-derived omega-3.',
            'dosage_heading_one' => 'Plant-derived omega-3',
            'dosage_heading_two' => '20 mg',
            'is_active' => true,
        ]);
        $product->ingredients()->attach($ingredient->id);

        $response = $this->get(route('product.show', 'brain-booster-gummies'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.product');
        $response->assertViewHas('product');
        $response->assertSee('/img/newwkadha.png', false);
        $response->assertSee('pdp-reference-features');
        $response->assertSee('assets/research/world-nutrition-map.html');
        $response->assertSee('assets/brain/routine-before.png');
        $response->assertSee('assets/brain/routine-after.webp');
        $response->assertSee('Reveal before and after routine images');
        $response->assertSee('pdp-formula-reveal');
        $response->assertSeeInOrder([
            'pdp-hero',
            'Expert Endorsement',
            'pdp-world-nutrition',
        ]);
        $response->assertSee('Meet your nutrition buddy');
        $response->assertSee('Little gummy.');
        $response->assertSee('Thoughtful formula.');
        $response->assertSee('Brain Booster Gummies');
        $response->assertSee('assets/brain/hero-reveal.webp');
        $response->assertSee('Ayurveda + nutraceutical');
        $response->assertSee('Brahmi & Shankhpushpi');
        $response->assertSee('20 mg');
        $response->assertSee('No preservatives');
        $response->assertSee('100% vegetarian');
        $response->assertSee('Choose your plan');
        $response->assertSee('Explore ingredients');
        $response->assertSee('Ingredient Transparency');
        $response->assertDontSee('nb-summary-bar', false);
        $response->assertSee('What’s inside');
        $response->assertSee('nb-ingredient-nav', false);
        $response->assertSee('role="tablist"', false);
        $ingredientCss = file_get_contents(public_path('assets/css/frontendstyle.css'));
        $this->assertMatchesRegularExpression(
            '/#nb-ingredients \.nb-detail-photo-wrap::before,\s*#nb-ingredients \.nb-detail-photo-wrap::after \{[^}]*border-radius: 50%;/s',
            $ingredientCss
        );
        $this->assertStringContainsString('width: min(74%, 220px);', $ingredientCss);
        $this->assertMatchesRegularExpression('/#nb-ingredients \.nb-mob-card-emoji \{[^}]*border-radius: 50%;/s', $ingredientCss);
        $this->assertMatchesRegularExpression(
            '/#nb-ingredients \.nb-mob-read-more \{[^}]*min-height: 44px;[^}]*white-space: nowrap;/s',
            $ingredientCss
        );
        $ingredientScript = file_get_contents(public_path('assets/js/frontendscript.js'));
        $this->assertStringContainsString('class="nb-mob-read-more-label">Read more</span>', $ingredientScript);
        $this->assertStringContainsString("label.textContent = expanded ? 'Read more' : 'Read less';", $ingredientScript);
        $mapDocument = file_get_contents(public_path('assets/research/world-nutrition-map.html'));
        $this->assertStringContainsString('Math.min(500,w*.4)', $mapDocument);
        $this->assertStringContainsString('max-width:1400px', $mapDocument);
        $this->assertStringContainsString('Published study location', $mapDocument);
        $this->assertStringContainsString('nb-map-legend', $mapDocument);
        $productCss = file_get_contents(public_path('assets/css/product.css'));
        $this->assertMatchesRegularExpression(
            '/\.pdp-formula-heading h2 \{[^}]*font-family: \'Nunito\', sans-serif;[^}]*font-weight: 800;/s',
            $productCss
        );
        $this->assertMatchesRegularExpression(
            '/\.pdp-formula-actions a \{[^}]*font-family: \'Nunito\', sans-serif;[^}]*font-weight: 800;/s',
            $productCss
        );
        $this->assertStringContainsString(
            '.pdp-formula-actions .pdp-formula-primary { background: var(--product-purple); color: #fff; }',
            $productCss
        );
        $this->assertMatchesRegularExpression(
            '/@media \(max-width: 767px\) \{\s*\.pdp-hero \.pdp-info \.pdp-offers-delivery \{\s*margin-bottom: 0px;\s*\}\s*\.pdp-accordion-section \{\s*padding-top: 0;\s*\}\s*\}/s',
            $productCss
        );
        $this->assertStringContainsString('padding: clamp(16px, 2vw, 24px) 24px clamp(32px, 4vw, 48px)', $productCss);
        $this->assertStringContainsString('grid-template-columns: repeat(5, minmax(0, 1fr))', $productCss);
        $this->assertMatchesRegularExpression(
            '/@media \(max-width: 767px\) \{\s*\.pdp-reference-features \{\s*grid-template-columns: repeat\(5, minmax\(0, 1fr\)\);/',
            $productCss
        );
        $response->assertSee('Children’s supplement use around the world');
        $response->assertSee('data-gallery-count', false);
        $response->assertSee('data-gallery-prev', false);
        $response->assertSee('data-gallery-next', false);
        $response->assertSee('data-gallery-zoom', false);
        $response->assertDontSee('pdp-zoom-source', false);
        $response->assertDontSee('pdp-zoom-lens', false);
        $response->assertDontSee('pdp-zoom-result', false);
        $zoomScript = file_get_contents(public_path('assets/js/pages/allfile.js'));
        $this->assertStringNotContainsString('initPdpImageZoom', $zoomScript);
        $this->assertStringNotContainsString('pdp-zoom-source', $zoomScript);
        $this->assertStringContainsString('thumbRow.scrollTo({', $zoomScript);
        $this->assertStringNotContainsString('el.scrollIntoView({', $zoomScript);
        $this->assertStringContainsString("select.addEventListener('change'", $zoomScript);
        $this->assertStringContainsString('option.disabled = !possible', $zoomScript);
        $response->assertSee('data-pdp-sticky-purchase', false);
        $response->assertSee('pdpStickyPrice', false);
        $response->assertSee('Red grape · 30 gummies · Ages 4+');
        $response->assertSee('data-pdp-sticky-action="cart"', false);
        $response->assertSee('data-pdp-sticky-action="buy"', false);
        $response->assertSee('EXPLORE THE NUTRIBUDDY FAMILY');
        $response->assertSee('One favourite.');
        $response->assertSee('Or a little more.');
        $response->assertSee('Individual products and thoughtful combos, all in one place.');
        $response->assertSee('data-pdp-family', false);
        $response->assertSee('Complete Care');
        $response->assertSee('Total Nutrition Kit');
        $response->assertSee('pdp-subscribe-story', false);
        $response->assertSee('Never run out.');
        $response->assertSee('Save from month two.');
        $response->assertSee('Pick your plan');
        $response->assertSee('See every payment');
        $response->assertSee('Stay in control');
        $response->assertSee('data-pdp-subscribe-cta', false);
        $response->assertSee('id="pdp-purchase-options"', false);
        $response->assertSee('Product description');
        $response->assertSee('What’s inside?');
        $response->assertSee('How do I use it?');
        $response->assertSee('How does Subscribe &amp; Save work?', false);
        $response->assertSee('What are the shipping and return policies?');
        $response->assertSeeInOrder([
            'Estimated delivery between',
            'Product description',
            'What’s inside?',
            'How do I use it?',
        ]);
        $response->assertSee('No Added');
        $response->assertSee('Colours');
        $response->assertSee('No Refined');
        $response->assertSee('Sugar');
        $response->assertSee('Preservatives');
        $response->assertSee('No<br>Gelatin', false);
        $response->assertSee('Vegetarian');
        $response->assertSee('pdp-subtitle');
        $response->assertSee('Algal DHA, Brahmi & Shankhpushpi, with essential nutrients.');
        $response->assertSee('1 review · design sample');
        $response->assertSee('5.0');
        $response->assertSee('₹649');
        $response->assertSee('₹699');
        $response->assertSee('pdp-price-line');
        $response->assertSee('pdp-coins-strip');
        $response->assertSeeInOrder([
            'pdp-price-line',
            'pdpVariantPanel',
            'How do you want to buy?',
        ]);
        $response->assertSeeInOrder([
            'data-attribute-group="Flavour"',
            'data-attribute-group="Size"',
            'pdp-suitable-age',
            'Suitable Age',
            '4+',
        ]);
        $response->assertDontSee('data-attribute-group="Age Group"', false);
        $response->assertSee('data-pdp-variant-select', false);
        $response->assertSee('value="Lemon Ginger"', false);
        $response->assertSee('value="Orange"', false);
        $response->assertSee('value="15pcs"', false);
        $response->assertSee('value="30pcs"', false);
        $response->assertSee('id="pdpSuitableAge"', false);
        $response->assertSee('value="4+"', false);
        $response->assertSee('pdpCoinsModal');
        $response->assertSee('A little thank-you from NutriBuddy');
        $response->assertSee('How do you want to buy?');
        $response->assertSee('One-time purchase');
        $response->assertSee('Subscribe & Save');
        $response->assertSee('From ₹569');
        $response->assertSee('Lower price from month 2');
        $response->assertDontSee('Plans coming soon');
        $response->assertSee('pdpSubscriptionPanel');
        $response->assertSee('Save from month 2 · Cancel anytime');
        $response->assertSee('2 months');
        $response->assertSee('3 months');
        $response->assertSee('4 months');
        $response->assertSee('Recommended');
        $response->assertSee('Best value');
        $response->assertSee('Free Tasty Kadha');
        $response->assertSee('Buy 2: save 5% · Buy 3+: save 10%');
        $response->assertSee('Available Coupons');
        $response->assertSee('Check eligible offers at checkout');
        $response->assertSee('pdpCouponModal');
        $response->assertSee('A little extra, on us');
        $response->assertSee('Extra 5% off prepaid orders');
        $response->assertSee('PREPAID5');
        $response->assertSee('Free shipping on ₹1,000+');
        $response->assertSee('Free shipping ₹1000+');
        $response->assertSee('COD Available');
        $productCss = file_get_contents(public_path('assets/css/product.css'));
        $this->assertStringContainsString('grid-template-columns: repeat(2, minmax(0, 1fr))', $productCss);
        $response->assertSee('Estimated delivery between');
        $response->assertSee('17 November – 19 November.');
        $response->assertDontSee('Why Parents Love Brain Booster Gummies');
        $response->assertDontSee('7-Day Return');
        $response->assertSee('Earn');
        $response->assertSee('32 NB');
    }

    public function test_authenticated_user_can_add_product_to_cart(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);

        $user = User::factory()->create();
        $product = Product::factory()->create([
            'is_active' => true,
            'base_price' => 350.00,
        ]);

        Inventory::factory()->create([
            'product_id' => $product->id,
            'product_variant_id' => null,
            'track_stock' => false,
            'stock_qty' => 10,
        ]);

        $response = $this->actingAs($user)->postJson(route('user.cart.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertSuccessful();
    }

    public function test_admin_with_permission_can_access_products_management_index(): void
    {
        Permission::create(['name' => 'view products', 'guard_name' => 'admin']);
        $role = Role::create(['name' => 'Catalog Manager', 'guard_name' => 'admin']);
        $role->givePermissionTo('view products');

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $admin->assignRole($role);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.ecommerce.products.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.ecommerce.products.index');
        $response->assertViewHas(['products', 'categories', 'taxRates']);
    }

    public function test_admin_product_resource_pages_use_the_listing_page_without_route_collision(): void
    {
        Permission::create(['name' => 'manage products', 'guard_name' => 'admin']);
        $role = Role::create(['name' => 'Product Manager', 'guard_name' => 'admin']);
        $role->givePermissionTo('manage products');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $admin->assignRole($role);
        $product = Product::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.ecommerce.products.create'))
            ->assertOk()
            ->assertViewIs('admin.ecommerce.products.create');

        $this->actingAs($admin, 'admin')
            ->get(route('admin.ecommerce.products.show', $product))
            ->assertRedirect(route('admin.ecommerce.products.index'));
    }

    public function test_admin_review_status_updates_keep_json_and_redirect_contracts(): void
    {
        Permission::create(['name' => 'manage moderation', 'guard_name' => 'admin']);
        $role = Role::create(['name' => 'Review Moderator', 'guard_name' => 'admin']);
        $role->givePermissionTo('manage moderation');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $admin->assignRole($role);
        $product = Product::factory()->create();
        $user = User::factory()->create();
        $review = ProductReview::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => 'Great product',
            'is_active' => true,
        ]);
        $videoReview = ProductReview::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => 'Video review',
            'video_path' => 'reviews/sample.mp4',
            'is_active' => true,
        ]);

        $this->actingAs($admin, 'admin')
            ->patchJson(route('admin.ecommerce.reviews.update', $review), ['is_active' => false])
            ->assertOk()
            ->assertExactJson(['success' => true, 'is_active' => false]);

        $this->patchJson(route('admin.ecommerce.video-reviews.update', $videoReview), ['is_active' => false])
            ->assertOk()
            ->assertExactJson(['success' => true, 'is_active' => false]);

        $this->from(route('admin.ecommerce.reviews.index'))
            ->patch(route('admin.ecommerce.reviews.update', $review), ['is_active' => true])
            ->assertRedirect(route('admin.ecommerce.reviews.index'))
            ->assertSessionHas('success', 'Review status updated successfully.');

        $this->assertDatabaseHas('product_reviews', ['id' => $review->id, 'is_active' => true]);
        $this->assertDatabaseHas('product_reviews', ['id' => $videoReview->id, 'is_active' => false]);
    }

    public function test_admin_product_problem_solution_hub_renders_correctly(): void
    {
        Permission::create(['name' => 'view products', 'guard_name' => 'admin']);
        $role = Role::create(['name' => 'Admin User', 'guard_name' => 'admin']);
        $role->givePermissionTo('view products');

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $admin->assignRole($role);

        $product = Product::factory()->create(['is_active' => true]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.ecommerce.products.problem-solution.index', ['product_id' => $product->id]));

        $response->assertStatus(200);
        $response->assertViewIs('admin.ecommerce.products.problem-solution');
        $response->assertViewHas('product');
    }

    public function test_admin_can_create_a_variant_and_inventory_record(): void
    {
        Permission::create(['name' => 'manage products', 'guard_name' => 'admin']);
        $role = Role::create(['name' => 'Variant Manager', 'guard_name' => 'admin']);
        $role->givePermissionTo('manage products');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $admin->assignRole($role);
        $product = Product::factory()->create(['product_type' => 'simple', 'is_variant_enabled' => false]);

        $this->actingAs($admin, 'admin')->post(route('admin.ecommerce.variants.store'), [
            'product_id' => $product->id,
            'name' => 'Small pack',
            'sku' => 'small-pack',
            'price' => 19.99,
            'stock_qty' => 7,
            'track_stock' => true,
            'is_in_stock' => true,
        ])->assertRedirect();

        $variant = ProductVariant::where('product_id', $product->id)->firstOrFail();
        $this->assertDatabaseHas('inventories', [
            'product_variant_id' => $variant->id,
            'stock_qty' => 7,
            'track_stock' => 1,
        ]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'product_type' => 'variable']);
    }

    public function test_admin_can_create_a_product_with_inventory(): void
    {
        Permission::create(['name' => 'manage products', 'guard_name' => 'admin']);
        $role = Role::create(['name' => 'Product Creator', 'guard_name' => 'admin']);
        $role->givePermissionTo('manage products');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $admin->assignRole($role);

        $this->actingAs($admin, 'admin')->post(route('admin.ecommerce.products.store'), [
            'name' => 'Daily Vitamin',
            'base_price' => 249,
            'product_type' => 'simple',
            'stock_qty' => 8,
        ])->assertRedirect(route('admin.ecommerce.products.index'));

        $product = Product::where('name', 'Daily Vitamin')->firstOrFail();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'slug' => 'daily-vitamin']);
        $this->assertDatabaseHas('inventories', ['product_id' => $product->id, 'stock_qty' => 8]);
    }

    public function test_admin_can_update_a_product_without_changing_its_route_contract(): void
    {
        Permission::create(['name' => 'manage products', 'guard_name' => 'admin']);
        $role = Role::create(['name' => 'Product Editor', 'guard_name' => 'admin']);
        $role->givePermissionTo('manage products');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $admin->assignRole($role);
        $product = Product::factory()->create(['name' => 'Old Name', 'slug' => 'old-name']);

        $this->actingAs($admin, 'admin')->put(route('admin.ecommerce.products.update', $product), [
            'name' => 'Updated Name',
            'base_price' => 399,
            'product_type' => 'simple',
            'stock_qty' => 3,
        ])->assertRedirect(route('admin.ecommerce.products.index'));

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Updated Name', 'slug' => 'updated-name']);
    }
}
