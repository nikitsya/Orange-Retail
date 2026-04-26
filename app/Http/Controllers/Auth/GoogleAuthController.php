<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        if (! $this->hasGoogleConfiguration()) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Google sign-in is not configured yet. Add the Google OAuth credentials first.',
                ]);
        }

        $state = Str::random(40);

        $request->session()->put('google_oauth_state', $state);

        return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => config('services.google.redirect'),
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'state' => $state,
            'prompt' => 'select_account',
        ]));
    }

    public function callback(Request $request): RedirectResponse
    {
        if ($request->filled('error')) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Google sign-in was cancelled. Please try again if you still want to continue.',
                ]);
        }

        $expectedState = (string) $request->session()->pull('google_oauth_state', '');
        $receivedState = (string) $request->query('state', '');

        if ($expectedState === '' || ! hash_equals($expectedState, $receivedState)) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Google sign-in could not be verified. Please start again from the login or register page.',
                ]);
        }

        $code = (string) $request->query('code', '');

        if ($code === '') {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Google did not return an authorization code.',
                ]);
        }

        $tokenResponse = Http::asForm()
            ->acceptJson()
            ->post('https://oauth2.googleapis.com/token', [
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect_uri' => config('services.google.redirect'),
                'grant_type' => 'authorization_code',
                'code' => $code,
            ]);

        if (! $tokenResponse->successful() || ! $tokenResponse->json('access_token')) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Google sign-in could not be completed. Please check the OAuth settings and try again.',
                ]);
        }

        $profileResponse = Http::acceptJson()
            ->withToken((string) $tokenResponse->json('access_token'))
            ->get('https://openidconnect.googleapis.com/v1/userinfo');

        if (! $profileResponse->successful()) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'We could not retrieve your Google profile information.',
                ]);
        }

        $profile = $profileResponse->json();
        $googleId = trim((string) ($profile['sub'] ?? ''));
        $email = Str::lower(trim((string) ($profile['email'] ?? '')));
        $name = trim((string) ($profile['name'] ?? 'Google User'));
        $avatar = trim((string) ($profile['picture'] ?? ''));
        $emailVerified = (bool) ($profile['email_verified'] ?? false);

        if ($googleId === '' || $email === '' || ! $emailVerified) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Your Google account must provide a verified email address to continue.',
                ]);
        }

        $user = User::query()
            ->where('google_id', $googleId)
            ->orWhere('email', $email)
            ->first();

        if ($user && $user->role !== 'user') {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Google sign-in is only available for regular customer accounts.',
                ]);
        }

        if (! $user) {
            $user = User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => Str::random(32),
                'role' => 'user',
                'google_id' => $googleId,
                'google_avatar' => $avatar !== '' ? $avatar : null,
                'email_verified_at' => now(),
            ]);
        } else {
            $user->forceFill([
                'name' => $name !== '' ? $name : $user->name,
                'email' => $email,
                'google_id' => $googleId,
                'google_avatar' => $avatar !== '' ? $avatar : $user->google_avatar,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    protected function hasGoogleConfiguration(): bool
    {
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'))
            && filled(config('services.google.redirect'));
    }
}
