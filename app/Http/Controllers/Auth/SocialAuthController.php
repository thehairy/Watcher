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
     * Redirect to the social provider.
     */
    public function redirect(string $provider)
    {
        $this->validateProvider($provider);
        
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the callback from the social provider.
     */
    public function callback(string $provider)
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Authentication failed. Please try again.');
        }

        // Check if user already exists with this social ID
        $user = User::where($provider . '_id', $socialUser->getId())->first();

        if ($user) {
            // User exists, log them in
            Auth::login($user, true);
            return redirect()->intended(route('dashboard'));
        }

        // Check if user exists with the same email
        $existingUser = User::where('email', $socialUser->getEmail())->first();

        if ($existingUser) {
            // Link the social account to existing user
            $existingUser->update([
                $provider . '_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
            ]);

            Auth::login($existingUser, true);
            return redirect()->intended(route('dashboard'));
        }

        // Create new user
        $username = $this->generateUniqueUsername($socialUser->getNickname() ?? $socialUser->getName());
        
        $user = User::create([
            'name' => $socialUser->getName(),
            'username' => $username,
            'email' => $socialUser->getEmail(),
            'avatar' => $socialUser->getAvatar(),
            $provider . '_id' => $socialUser->getId(),
            'email_verified_at' => now(),
        ]);

        Auth::login($user, true);
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Validate the social provider.
     */
    private function validateProvider(string $provider): void
    {
        if (!in_array($provider, ['discord', 'github', 'steam'])) {
            abort(404);
        }
    }

    /**
     * Generate a unique username.
     */
    private function generateUniqueUsername(string $baseName): string
    {
        $username = Str::slug($baseName, '');
        $originalUsername = $username;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $originalUsername . $counter;
            $counter++;
        }

        return $username;
    }
}
