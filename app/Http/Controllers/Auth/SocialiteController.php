<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect(): RedirectResponse
    {
        $driver = Socialite::driver('google');

        Log::info('Google OAuth redirect initiated', [
            'session_id' => session()->getId(),
            'session_driver' => config('session.driver'),
            'session_domain' => config('session.domain'),
            'session_secure' => config('session.secure'),
            'session_same_site' => config('session.same_site'),
            'session_lifetime' => config('session.lifetime'),
            'app_url' => config('app.url'),
            'google_redirect_uri' => config('services.google.redirect'),
            'session_keys' => array_keys(session()->all()),
            'has_socialite_state' => session()->has('socialite_oauth_state'),
        ]);

        return $driver->redirect();
    }

    /**
     * Handle the Google callback.
     */
    public function callback(): RedirectResponse
    {
        Log::info('Google OAuth callback received', [
            'session_id' => session()->getId(),
            'session_driver' => config('session.driver'),
            'session_domain' => config('session.domain'),
            'session_secure' => config('session.secure'),
            'session_same_site' => config('session.same_site'),
            'session_keys' => array_keys(session()->all()),
            'has_socialite_state' => session()->has('socialite_oauth_state'),
            'socialite_state_value' => session()->get('socialite_oauth_state') ? 'EXISTS (hidden)' : 'MISSING',
            'request_has_state' => request()->has('state'),
            'request_state_length' => request()->input('state') ? strlen(request()->input('state')) : 0,
            'request_has_code' => request()->has('code'),
            'request_uri' => request()->getRequestUri(),
            'cf_clearance_cookie' => request()->cookies->has('cf_clearance'),
        ]);

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            Log::error('Google OAuth InvalidStateException caught', [
                'session_id' => session()->getId(),
                'session_driver' => config('session.driver'),
                'session_keys' => array_keys(session()->all()),
                'has_socialite_state' => session()->has('socialite_oauth_state'),
                'exception_message' => $e->getMessage(),
                'request_has_state' => request()->has('state'),
                'request_has_code' => request()->has('code'),
            ]);

            return redirect()->route('login')
                ->with('error', 'Terjadi kesalahan saat login dengan Google. Silakan coba lagi.');
        }

        $user = User::where('google_id', $googleUser->id)
            ->orWhere('email', $googleUser->email)
            ->first();

        if ($user) {
            $user->update([
                'google_id' => $googleUser->id,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
        } else {
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
