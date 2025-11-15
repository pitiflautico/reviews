<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user already exists with this Google ID
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user) {
                // Update avatar if changed
                if ($user->avatar !== $googleUser->getAvatar()) {
                    $user->update(['avatar' => $googleUser->getAvatar()]);
                }
            } else {
                // Check if user exists with this email
                $user = User::where('email', $googleUser->getEmail())->first();

                if ($user) {
                    // Link Google account to existing user
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                    ]);
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'password' => Hash::make(Str::random(24)), // Random password for security
                        'email_verified_at' => now(), // Google accounts are pre-verified
                    ]);
                }
            }

            // Log the user in
            Auth::login($user, true);

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Error al autenticar con Google. Por favor intenta nuevamente.');
        }
    }
}
