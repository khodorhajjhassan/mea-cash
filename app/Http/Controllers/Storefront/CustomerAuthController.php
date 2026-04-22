<?php

namespace App\Http\Controllers\Storefront;
 
use App\Http\Controllers\Controller;
use App\Mail\RegistrationOtpMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class CustomerAuthController extends Controller
{
    public function showLogin()
    {
        return view('storefront.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        try {
            if (! Auth::attempt($credentials, $request->boolean('remember'))) {
                return back()->withInput()->withErrors([
                    'email' => __('storefront.auth.invalid_credentials'),
                ]);
            }

            $request->session()->regenerate();

            if (! Auth::user()?->email_verified_at) {
                $user = Auth::user();
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                $request->session()->put('pending_registration_user_id', $user->id);
                $request->session()->put('pending_registration_email', $user->email);

                return redirect()->route('store.register.verify')
                    ->with('error', app()->getLocale() === 'ar' ? 'يرجى تأكيد بريدك الإلكتروني أولاً.' : 'Please verify your email first.');
            }

            if (! Auth::user()?->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withInput()->withErrors([
                    'email' => __('storefront.auth.account_inactive'),
                ]);
            }

            // If admin, redirect to admin dashboard
            if (Auth::user()?->is_admin) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended(route('store.dashboard'))
                ->with('success', __('storefront.auth.welcome_back'));
        } catch (\Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', __('storefront.auth.login_failed'));
        }
    }

    public function showRegister()
    {
        return view('storefront.auth.register');
    }

    public function showVerifyOtp(Request $request)
    {
        if (! $request->session()->has('pending_registration_user_id')) {
            return redirect()->route('store.register');
        }

        return view('storefront.auth.verify-otp', [
            'email' => $request->session()->get('pending_registration_email'),
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'preferred_language' => ['required', 'in:ar,en'],
            'terms' => ['accepted'],
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'preferred_language' => $validated['preferred_language'] ?? app()->getLocale(),
                'is_active' => true,
                'is_admin' => false,
            ]);

            $this->sendVerificationCode($user);

            $request->session()->put('pending_registration_user_id', $user->id);
            $request->session()->put('pending_registration_email', $user->email);

            return redirect()->route('store.register.verify')
                ->with('success', app()->getLocale() === 'ar'
                    ? 'تم إرسال رمز التأكيد إلى بريدك الإلكتروني.'
                    : 'We sent a verification code to your email.');
        } catch (\Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', __('storefront.auth.register_failed'));
        }
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = User::query()->find($request->session()->get('pending_registration_user_id'));
        if (! $user) {
            return redirect()->route('store.register');
        }

        if (
            ! hash_equals((string) $user->email_verification_code, (string) $validated['code'])
            || ! $user->email_verification_expires_at
            || now()->gt($user->email_verification_expires_at)
        ) {
            return back()->withErrors([
                'code' => app()->getLocale() === 'ar' ? 'رمز التأكيد غير صحيح أو منتهي الصلاحية.' : 'The verification code is invalid or expired.',
            ]);
        }

        $user->update([
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_expires_at' => null,
        ]);

        $request->session()->forget(['pending_registration_user_id', 'pending_registration_email']);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('store.dashboard')->with('success', __('storefront.auth.welcome'));
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        $user = User::query()->find($request->session()->get('pending_registration_user_id'));
        if (! $user) {
            return redirect()->route('store.register');
        }

        $this->sendVerificationCode($user);

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'تم إرسال رمز جديد إلى بريدك الإلكتروني.'
            : 'A new verification code was sent to your email.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('store.home')
            ->with('success', __('storefront.auth.logged_out'));
    }

    private function sendVerificationCode(User $user): void
    {
        $code = (string) random_int(100000, 999999);
        $user->forceFill([
            'email_verification_code' => $code,
            'email_verification_expires_at' => now()->addMinutes(15),
        ])->save();

        Mail::to($user->email)->queue(new RegistrationOtpMail(
            user: $user,
            code: $code,
            mailLocale: $user->preferred_language ?: app()->getLocale(),
        ));
    }
}
