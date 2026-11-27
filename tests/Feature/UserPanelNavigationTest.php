<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPanelNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_navigation_stays_in_the_sidebar_on_user_panel_pages(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('userdashboard'));

        $response->assertOk();
        preg_match('/<ul class="nav-links">(.*?)<\/ul>/s', $response->getContent(), $matches);

        $this->assertNotEmpty($matches[1] ?? null, 'The desktop navigation list was not rendered.');
        $this->assertStringContainsString('Ingredients', $matches[1]);
        $this->assertStringNotContainsString('Overview', $matches[1]);
        $this->assertStringNotContainsString('Meal Plan', $matches[1]);
        $this->assertStringNotContainsString('Growth Signals', $matches[1]);
        $this->assertStringContainsString('Overview', $response->getContent());
    }

    public function test_dashboard_header_does_not_show_the_quarterly_check_in_button(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('userdashboard'))
            ->assertOk()
            ->assertDontSee('btn-quarterly')
            ->assertSee('Quarterly Check-in');
    }

    public function test_dashboard_hides_the_static_sample_diet_section(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('userdashboard'))
            ->assertOk()
            ->assertDontSee('Good morning, Priya')
            ->assertDontSee('Greeshma — Summer Season Plan')
            ->assertDontSee('Turmeric milk')
            ->assertDontSee("Arjun's Health Score");
    }

    public function test_dashboard_quick_actions_use_icons_and_link_to_their_pages(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('userdashboard'));
        $response->assertOk();

        preg_match_all('/<a href="([^"]+)" class="qa-btn d[3-6]">.*?<\/a>/s', $response->getContent(), $matches);

        $this->assertSame([
            route('product'),
            route('order'),
            route('my-coupons'),
            route('meal-plan'),
        ], $matches[1]);
        $this->assertSame(4, substr_count(implode('', $matches[0]), 'class="qa-icon"'));
    }

    public function test_health_score_card_offers_assessment_when_the_user_has_not_taken_one(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('userdashboard'))
            ->assertOk()
            ->assertSee('Take Assessment')
            ->assertSee('href="' . route('user.assessment.index') . '"', false);
    }
}
