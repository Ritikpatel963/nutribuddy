<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UserDashboardRecentReviewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_only_the_users_two_most_recent_reviews(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->createReview($user, 'Older review product', 'Older review', now()->subDays(3));
        $this->createReview($user, 'Recent review product', 'Recent review', now()->subDay());
        $this->createReview($user, 'Newest review product', 'Newest review', now());
        $this->createReview($otherUser, 'Another users product', 'Another users review', now());

        $response = $this->actingAs($user)->get(route('userdashboard'));
        $response->assertOk()
            ->assertSee('Recent review')
            ->assertSee('Newest review')
            ->assertSee(route('user.reviews.index'));

        preg_match_all('/<div class="rp">([^<]*)<\/div>/', $response->getContent(), $productNames);
        $this->assertSame(['Newest review product', 'Recent review product'], $productNames[1]);
    }

    public function test_dashboard_prompts_the_user_to_review_an_ordered_product_when_they_have_no_reviews(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->createReview($otherUser, 'Another users product', 'Another users review', now());

        $response = $this->actingAs($user)->get(route('userdashboard'));
        $response->assertOk()
            ->assertSee('You have not reviewed a product from your orders yet.')
            ->assertSee('Review an ordered product')
            ->assertSee(route('user.reviews.index'));

        preg_match_all('/<div class="rp">([^<]*)<\/div>/', $response->getContent(), $productNames);
        $this->assertSame([], $productNames[1]);
    }

    private function createReview(
        User $user,
        string $productName,
        string $comment,
        Carbon $createdAt,
    ): ProductReview {
        $product = Product::factory()->create(['name' => $productName]);

        $review = ProductReview::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => $comment,
            'is_active' => true,
        ]);

        $review->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->save();

        return $review;
    }
}
