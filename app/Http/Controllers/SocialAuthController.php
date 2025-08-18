<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the OAuth provider authentication page.
     */
    public function redirect(string $provider)
    {
        $this->validateProvider($provider);
        
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from the OAuth provider.
     */
    public function callback(string $provider)
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Authentication failed. Please try again.');
        }

        // Find existing user or create new one
        $user = $this->findOrCreateUser($socialUser, $provider);

        Auth::login($user, true);

        return redirect()->intended('/dashboard');
    }

    /**
     * Find existing user or create new one based on social provider data.
     */
    private function findOrCreateUser($socialUser, string $provider): User
    {
        $providerIdField = $provider . '_id';

        // First, try to find user by provider ID
        $user = User::where($providerIdField, $socialUser->getId())->first();

        if ($user) {
            // Update user info if needed
            $this->updateUserFromSocial($user, $socialUser, $provider);
            return $user;
        }

        // If user has email, try to find by email (for linking accounts)
        if (!empty($socialUser->getEmail())) {
            $user = User::where('email', $socialUser->getEmail())->first();
            
            if ($user) {
                // Link this social account to existing user
                $user->update([
                    $providerIdField => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar() ?? $user->avatar,
                ]);
                return $user;
            }
        }

        // Create new user
        return $this->createUserFromSocial($socialUser, $provider);
    }

    /**
     * Create a new user from social provider data.
     */
    private function createUserFromSocial($socialUser, string $provider): User
    {
        $providerIdField = $provider . '_id';
        
        // Generate username from name or provider nickname
        $name = $socialUser->getName() ?? $socialUser->getNickname() ?? 'User';
        $baseUsername = Str::slug($name, '');
        $username = $this->generateUniqueUsername($baseUsername);

        $userData = [
            'name' => $name,
            'username' => $username,
            'email' => $socialUser->getEmail(), // This can be null for Steam
            'avatar' => $socialUser->getAvatar(),
            $providerIdField => $socialUser->getId(),
            'password' => null, // Social-only accounts don't need password
        ];

        // Remove null email if provider doesn't provide it (like Steam)
        if (empty($userData['email'])) {
            unset($userData['email']);
        }

        return User::create($userData);
    }

    /**
     * Update existing user with fresh social data.
     */
    private function updateUserFromSocial(User $user, $socialUser, string $provider): void
    {
        $updates = [
            'avatar' => $socialUser->getAvatar() ?? $user->avatar,
        ];

        // Only update email if user doesn't have one and provider provides it
        if (empty($user->email) && !empty($socialUser->getEmail())) {
            $updates['email'] = $socialUser->getEmail();
        }

        $user->update($updates);
    }

    /**
     * Generate a unique username.
     */
    private function generateUniqueUsername(string $baseUsername): string
    {
        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Validate that the provider is supported.
     */
    private function validateProvider(string $provider): void
    {
        if (!in_array($provider, ['discord', 'github', 'steam'])) {
            abort(404);
        }
    }
}
