<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class GooglePasswordSetupController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        abort_unless($request->user()->google_id && $request->user()->password === null, 403);

        $status = Password::sendResetLink(['email' => $request->user()->email]);

        if ($status !== Password::RESET_LINK_SENT) {
            return back()->withErrors(['password_setup' => __($status)]);
        }

        // The existing Breeze reset form is guest-only. Sign out after sending the link.
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', __($status));
    }
}
