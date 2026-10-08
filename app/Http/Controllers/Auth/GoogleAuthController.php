<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (! $this->configured()) {
            return $this->failure('Google sign-in is not configured yet. Please use email and password.');
        }

        try {
            // Use Socialite's session-backed state validation; never use stateless().
            return Socialite::driver('google')->redirect();
        } catch (Exception $exception) {
            return $this->failure('Google sign-in is temporarily unavailable. Please try again.');
        }
    }

    public function callback(Request $request): RedirectResponse
    {
        if (! $this->configured()) {
            return $this->failure('Google sign-in is not configured yet. Please use email and password.');
        }

        if ($request->has('error') || ! $request->filled('code')) {
            $request->session()->forget('state');

            return $this->failure('Google sign-in was cancelled or incomplete. Please try again.');
        }

        try {
            $identity = Socialite::driver('google')->user();
        } catch (InvalidStateException $exception) {
            return $this->failure('Your Google sign-in session expired. Please start again.');
        } catch (Exception $exception) {
            // Do not log exception bodies: they may contain OAuth tokens or codes.
            return $this->failure('Google sign-in is temporarily unavailable. Please try again.');
        }

        $googleId = (string) $identity->getId();
        $email = strtolower((string) $identity->getEmail());
        $verified = $identity->user['email_verified'] ?? $identity->user['verified_email'] ?? false;

        if ($googleId === '' || strlen($googleId) > 255 || $verified !== true
            || ! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
            return $this->failure('Google did not provide a valid, verified identity. Please use another sign-in method.');
        }

        // Stable provider ID is the identity. Never auto-link an existing email account.
        $user = User::where('google_id', $googleId)->first();

        if (! $user) {
            if (User::whereRaw('LOWER(email) = ?', [$email])->exists()) {
                return $this->failure('An account already uses this email. Sign in with its existing method; Google accounts are not linked automatically.');
            }

            try {
                $user = new User;
                $user->name = mb_substr($identity->getName() ?: 'Google user', 0, 255);
                $user->email = $email;
                $user->google_id = $googleId;
                $user->email_verified_at = now();
                $user->password = null;
                $user->save();
            } catch (UniqueConstraintViolationException $exception) {
                return $this->failure('This account changed during sign-in. Please start again using its existing sign-in method.');
            }

            event(new Registered($user));
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    private function configured(): bool
    {
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'))
            && filled(config('services.google.redirect'));
    }

    private function failure(string $message): RedirectResponse
    {
        return redirect()->route('login')->withErrors(['google' => $message]);
    }
}
