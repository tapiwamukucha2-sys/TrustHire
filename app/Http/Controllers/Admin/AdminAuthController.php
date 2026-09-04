<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetCode;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    private const PASSWORD_REGEX = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/';

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt(['username' => $data['username'], 'password' => $data['password']])
            || ! Auth::user()->is_admin) {
            Auth::logout();

            return back()->withErrors(['username' => 'Invalid admin credentials.'])->onlyInput('username');
        }

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function showForgotPassword(): View
    {
        return view('admin.forgot-password');
    }

    public function sendResetCode(Request $request): RedirectResponse
    {
        $data = $request->validate(['username' => 'required|string']);

        $key = 'admin-password-reset-send:'.$data['username'];
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'username' => 'Too many reset requests. Try again in a few minutes.',
            ]);
        }
        RateLimiter::hit($key, 900);

        if (! User::where('username', $data['username'])->where('is_admin', true)->exists()) {
            return back()->withErrors(['username' => 'No admin account found with that username.']);
        }

        $code = PasswordResetCode::generateFor($data['username']);

        // Dev stub: no email/SMS provider wired up yet, so the code is flashed back to the caller.
        return redirect()->route('admin.password.reset', ['username' => $data['username']])
            ->with('devCode', $code);
    }

    public function showResetPassword(Request $request): View
    {
        return view('admin.reset-password', ['username' => $request->query('username', '')]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => 'required|string',
            'code' => 'required|string',
            'password' => 'required|string',
            'password_confirmation' => 'required|string|same:password',
        ]);

        if (! preg_match(self::PASSWORD_REGEX, $data['password'])) {
            return back()->withErrors([
                'password' => 'Password must be at least 8 characters and include uppercase, lowercase, a number, and a special character.',
            ])->with('username', $data['username']);
        }

        if (! PasswordResetCode::verify($data['username'], $data['code'])) {
            return back()->withErrors(['code' => 'Invalid or expired code.'])->with('username', $data['username']);
        }

        $user = User::where('username', $data['username'])->where('is_admin', true)->first();
        $user->update(['password' => Hash::make($data['password'])]);

        return redirect()->route('admin.login')->with('status', 'Password reset. You can now log in.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
