<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FrontendAuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_development_otp_is_stored_as_a_hash_and_omitted_from_json(): void
    {
        $response = $this->postJson(route('frontend.sendOtp'), [
            'phone' => '9876543210',
        ])->assertOk();

        $payload = $response->json();

        $this->assertArrayNotHasKey('otp', $payload);
        $this->assertTrue(Hash::check('123456', User::where('phone', '9876543210')->value('otp')));
    }

    public function test_production_otp_is_random_delivered_and_verified(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);
        $this->app->detectEnvironment(fn () => 'production');
        config([
            'services.aisensy.enabled' => true,
            'services.aisensy.api_key' => 'test-api-key',
            'services.aisensy.otp_campaign' => 'customer-otp',
        ]);
        Http::fake();
        $sentOtp = null;

        $this->postJson(route('frontend.sendOtp'), ['phone' => '9876543210'])
            ->assertOk()
            ->assertJsonMissingPath('otp');

        Http::assertSent(function (HttpRequest $request) use (&$sentOtp): bool {
            $sentOtp = data_get($request->data(), 'templateParams.0');

            return $request->url() === config('services.aisensy.endpoint')
                && data_get($request->data(), 'campaignName') === 'customer-otp'
                && is_string($sentOtp)
                && preg_match('/^\d{6}$/', $sentOtp) === 1;
        });

        $user = User::where('phone', '9876543210')->firstOrFail();
        $this->assertTrue(Hash::check($sentOtp, $user->otp));

        $this->postJson(route('frontend.verifyOtp'), [
            'phone' => $user->phone,
            'otp' => $sentOtp,
        ])->assertOk()->assertJsonPath('success', true);
    }

    public function test_verify_otp_rejects_external_redirects(): void
    {
        $user = User::factory()->create([
            'phone' => '9876543211',
            'role' => 'customer',
            'otp' => Hash::make('654321'),
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        $this->postJson(route('frontend.verifyOtp'), [
            'phone' => $user->phone,
            'otp' => '654321',
            'redirect_to' => 'https://example.com/phishing',
        ])
            ->assertOk()
            ->assertJsonPath('redirect', route('userdashboard'));
    }
}
