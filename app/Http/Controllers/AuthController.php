<?php

namespace App\Http\Controllers;

use App\Models\OtpCode;
use App\Models\PasswordResetCode;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const PASSWORD_REGEX = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/';

    public function showLogin(Request $request)
    {
        return view('auth.login', [
            'callbackUrl' => $request->query('callback', '/'),
        ]);
    }

    public function sendOtp(Request $request): RedirectResponse
    {
        $data = $request->validate(['phone' => 'required|string']);
        $phone = $data['phone'];

        $key = 'otp-send:'.$phone;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'phone' => 'Too many code requests for this number. Try again in a few minutes.',
            ]);
        }
        RateLimiter::hit($key, 900);

        $code = (string) random_int(100000, 999999);
        OtpCode::create([
            'phone' => $phone,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Dev stub: no SMS provider wired up yet, so the code is flashed back to the caller.
        return redirect()->route('login', ['callback' => $request->query('callback', '/')])
            ->with('phone', $phone)
            ->with('devCode', $code)
            ->with('step', 'code');
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string',
        ]);

        $otp = OtpCode::where('phone', $data['phone'])
            ->where('code', $data['code'])
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();

        if (! $otp) {
            return redirect()->route('login', ['callback' => $request->query('callback', '/')])
                ->with('phone', $data['phone'])
                ->with('step', 'code')
                ->withErrors(['code' => 'Invalid or expired code.']);
        }

        $otp->update(['consumed_at' => now()]);

        $user = User::firstOrCreate(['phone' => $data['phone']]);
        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectAfterLogin($request);
    }

    public function registerEmail(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'password_confirmation' => 'required|string|same:password',
        ]);

        $email = strtolower($data['email']);

        if (! preg_match(self::PASSWORD_REGEX, $data['password'])) {
            return back()->withErrors([
                'password' => 'Password must be at least 8 characters and include uppercase, lowercase, a number, and a special character.',
            ])->with('tab', 'email')->with('emailMode', 'signup');
        }

        if (User::where('email', $email)->exists()) {
            return back()->withErrors(['email' => 'An account with this email already exists.'])
                ->with('tab', 'email')->with('emailMode', 'signup');
        }

        $user = User::create([
            'email' => $email,
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectAfterLogin($request);
    }

    public function loginEmail(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = strtolower($data['email']);
        $key = 'login-email:'.$email;

        if (RateLimiter::tooManyAttempts($key, 8)) {
            return back()->withErrors(['email' => 'Too many attempts. Please try again later.'])
                ->with('tab', 'email');
        }

        if (! Auth::attempt(['email' => $email, 'password' => $data['password']])) {
            RateLimiter::hit($key, 900);

            return back()->withErrors(['email' => 'Invalid email or password.'])->with('tab', 'email');
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return $this->redirectAfterLogin($request);
    }

    public function showForgotPassword(): \Illuminate\View\View
    {
        return view('auth.forgot-password');
    }

    public function sendResetCode(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => 'required|email']);
        $email = strtolower($data['email']);

        $key = 'password-reset-send:'.$email;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Too many reset requests for this email. Try again in a few minutes.',
            ]);
        }
        RateLimiter::hit($key, 900);

        if (! User::where('email', $email)->exists()) {
            return back()->withErrors(['email' => 'No account found with that email.']);
        }

        $code = PasswordResetCode::generateFor($email);

        // Dev stub: no email provider wired up yet, so the code is flashed back to the caller.
        return redirect()->route('password.reset', ['email' => $email])
            ->with('devCode', $code);
    }

    public function showResetPassword(Request $request): \Illuminate\View\View
    {
        return view('auth.reset-password', ['email' => $request->query('email', '')]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
            'password' => 'required|string',
            'password_confirmation' => 'required|string|same:password',
        ]);

        $email = strtolower($data['email']);

        if (! preg_match(self::PASSWORD_REGEX, $data['password'])) {
            return back()->withErrors([
                'password' => 'Password must be at least 8 characters and include uppercase, lowercase, a number, and a special character.',
            ])->with('email', $email);
        }

        if (! PasswordResetCode::verify($email, $data['code'])) {
            return back()->withErrors(['code' => 'Invalid or expired code.'])->with('email', $email);
        }

        $user = User::where('email', $email)->first();
        $user->update(['password' => Hash::make($data['password'])]);

        return redirect()->route('login')->with('status', 'Password reset. You can now log in.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function redirectAfterLogin(Request $request): RedirectResponse
    {
        $callback = $request->input('callback', $request->query('callback', '/'));

        return redirect()->route('onboarding.show', ['callback' => $callback]);
    }
}
