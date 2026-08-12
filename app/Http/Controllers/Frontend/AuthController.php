<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return redirect()->route('home', ['login' => 1]);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric|digits:10',
        ]);

        $phone = $request->phone;
        $otp = '123456';
        $expiresAt = now()->addMinutes(10);

        $user = User::where('phone', $phone)->first();
        if ($user && $user->role !== 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'Admin accounts cannot login via OTP. Please use the admin portal.'
            ], 403);
        }

        if (!$user) {
            $user = User::create([
                'name' => 'Customer ' . substr($phone, -4),
                'phone' => $phone,
                'email' => $phone . '@nutribuddy.com',
                'password' => Hash::make(Str::random(16)),
                'role' => 'customer',
            ]);
        }

        $user->otp = $otp;
        $user->otp_expires_at = $expiresAt;
        $user->save();

        if (app()->environment('local')) {
            Log::info("Frontend OTP for {$phone}: {$otp}");
        }

        $payload = [
            'success' => true,
            'message' => 'OTP sent successfully. Use 123456.',
        ];

        if (app()->environment('local')) {
            $payload['otp'] = $otp;
        }

        return response()->json($payload);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric|digits:10',
            'otp' => 'required|numeric|digits:6',
        ]);

        $user = User::where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->where('otp_expires_at', '>', now())
            ->first();

        if ($user) {
            if ($user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin accounts cannot login via OTP.'
                ], 403);
            }

            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been deactivated. Please contact support.'
                ], 403);
            }

            $user->otp = null;
            $user->otp_expires_at = null;
            $user->save();

            Auth::guard('web')->login($user, true);
            $request->session()->regenerate();

            $redirect = $this->safeRedirect($request->redirect_to);

            return response()->json([
                'success' => true,
                'message' => 'Logged in successfully.',
                'redirect' => $redirect,
                'csrf_token' => csrf_token(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid or expired OTP.'
        ], 422);
    }

    public function logout(Request $request)
    {
        // Only log out the frontend 'web' guard.
        // Do NOT call $request->session()->invalidate() here because that
        // destroys the ENTIRE session (including the admin guard session),
        // which would log the admin out of the backend panel too.
        Auth::guard('web')->logout();

        return redirect()->route('home')->with('success', 'Logged out successfully.');
    }

    private function safeRedirect(?string $redirectTo): string
    {
        if (! $redirectTo) {
            return route('userdashboard');
        }

        if (str_starts_with($redirectTo, '/') && ! str_starts_with($redirectTo, '//')) {
            return $redirectTo;
        }

        if (URL::isValidUrl($redirectTo) && parse_url($redirectTo, PHP_URL_HOST) === request()->getHost()) {
            return $redirectTo;
        }

        return route('userdashboard');
    }
}
