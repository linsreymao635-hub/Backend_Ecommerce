<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SocialAuthController extends Controller
{
    // Redirect to Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Google callback
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate(
                ['google_id' => $googleUser->getId()],
                [
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(uniqid()),
                ]
            );

            $token = $user->createToken('auth_token')->plainTextToken;

            // Redirect to frontend with token
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            return redirect()->away($frontendUrl . '/auth/callback?token=' . $token . '&user=' . urlencode(base64_encode(json_encode($user))));

        } catch (\Exception $e) {
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            return redirect()->away($frontendUrl . '/auth/callback?error=' . urlencode($e->getMessage()));
        }
    }

    // Redirect to Facebook
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    // Facebook callback
    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();

            $user = User::updateOrCreate(
                ['facebook_id' => $facebookUser->getId()],
                [
                    'name' => $facebookUser->getName(),
                    'email' => $facebookUser->getEmail(),
                    'password' => Hash::make(uniqid()),
                ]
            );

            $token = $user->createToken('auth_token')->plainTextToken;

            // Redirect to frontend with token
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            return redirect()->away($frontendUrl . '/auth/callback?token=' . $token . '&user=' . urlencode(base64_encode(json_encode($user))));

        } catch (\Exception $e) {
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            return redirect()->away($frontendUrl . '/auth/callback?error=' . urlencode($e->getMessage()));
        }
    }

    // Redirect to GitHub
    public function redirectToGithub()
    {
        return Socialite::driver('github')->redirect();
    }

    // GitHub callback
    public function handleGithubCallback()
    {
        try {
            $githubUser = Socialite::driver('github')->user();

            $user = User::updateOrCreate(
                ['github_id' => $githubUser->getId()],
                [
                    'name' => $githubUser->getName() ?? $githubUser->getNickname(),
                    'email' => $githubUser->getEmail(),
                    'password' => Hash::make(uniqid()),
                ]
            );

            $token = $user->createToken('auth_token')->plainTextToken;

            // Redirect to frontend with token
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            return redirect()->away($frontendUrl . '/auth/callback?token=' . $token . '&user=' . urlencode(base64_encode(json_encode($user))));

        } catch (\Exception $e) {
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            return redirect()->away($frontendUrl . '/auth/callback?error=' . urlencode($e->getMessage()));
        }
    }
}
