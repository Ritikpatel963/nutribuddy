<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendAuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_otp_does_not_expose_code_outside_local_environment(): void
    {
        $response = $this->postJson(route('frontend.sendOtp'), [
            'phone' => '9876543210',
        ])->assertOk();

        $payload = $response->json();

        $this->assertArrayNotHasKey('otp', $payload);
        $this->assertNotSame('123456', User::where('phone', '9876543210')->value('otp'));
    }

    public function test_verify_otp_rejects_external_redirects(): void
    {
        $user = User::factory()->create([
            'phone' => '9876543211',
            'role' => 'customer',
            'otp' => '654321',
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
