<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToProvider($provider)
    {
        if (!in_array($provider, ['google', 'github'])) {
            return redirect('/login')->with('error', 'Invalid provider');
        }

        return Socialite::driver($provider)->redirect();
    }
    public function handleProviderCallback($provider, Request $request)
    {

        if (!in_array($provider, ['google', 'github'])) {
            return redirect('/login')->with('error', 'Invalid provider');
        }
        try {
            $socialUser = Socialite::driver($provider)->user();

            // Check if user has a valid email
            if (empty($socialUser->getEmail())) {
                return redirect('/login')->withErrors(['email' => 'No email address was provided from the social account.']);
            }

            // Find or create the user
            $user = User::updateOrCreate([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
            ], [
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'username' => $this->generateUniqueUsername($socialUser->getNickname() ?? $socialUser->getName()),
                'avatar' => $socialUser->getAvatar(),
            ]);

            Auth::login($user);

            return redirect('/dashboard');
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['msg' => 'An error occurred during authentication. Please try again.']);
        }
    }
    /**
     * Generate a unique username.
     */
    private function generateUniqueUsername($name): string
    {
        $username = Str::slug($name);
        $originalUsername = $username;
        $count = 1;

        while (User::where('username', $username)->exists()) {
            $username = $originalUsername . $count;
            $count++;
        }

        return $username;
    }
}
