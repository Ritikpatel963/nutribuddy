<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        $otp = '123456'; // Dummy OTP for testing
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

        Log::info("Frontend Dummy OTP for $phone: $otp");

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully (Use 123456).',
            'otp' => $otp
        ]);
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

            $user->otp = null;
            $user->otp_expires_at = null;
            $user->save();

            Auth::login($user, true);
            $request->session()->regenerate();

            $redirect = $request->redirect_to ?: route('userdashboard');

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
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logged out successfully.');
    }
}
