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
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Laravel\Socialite\Facades\Socialite;

class CustomerAuthController extends Controller
{
    public function showLogin(Request $request)
    {
        $this->rememberIntendedUrl($request);

        return view('storefront.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $this->rememberIntendedUrl($request);

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
                return $this->completeAuthentication(
                    $request,
                    Auth::user(),
                    __('storefront.auth.welcome_back'),
                );
            }

            return $this->completeAuthentication(
                $request,
                Auth::user(),
                __('storefront.auth.welcome_back'),
            );
        } catch (\Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', __('storefront.auth.login_failed'));
        }
    }

    public function showRegister(Request $request)
    {
        $this->rememberIntendedUrl($request);

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
        $this->rememberIntendedUrl($request);

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

        return $this->completeAuthentication(
            $request,
            $user,
            __('storefront.auth.welcome'),
        );
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

    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $this->rememberIntendedUrl($request);
        $request->session()->put('google_auth_locale', app()->getLocale());

        if (! $this->googleIsConfigured()) {
            return redirect()->route('login', ['locale' => app()->getLocale()])
                ->with('error', __('storefront.auth.google_not_configured'));
        }

        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        $locale = $request->session()->get('google_auth_locale', app()->getLocale());
        app()->setLocale(in_array($locale, ['en', 'ar'], true) ? $locale : config('app.locale'));

        if (! $this->googleIsConfigured()) {
            return redirect()->route('login', ['locale' => app()->getLocale()])
                ->with('error', __('storefront.auth.google_not_configured'));
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->route('login', ['locale' => app()->getLocale()])
                ->with('error', __('storefront.auth.google_failed'));
        }

        if (! filled($googleUser->getEmail())) {
            return redirect()->route('login', ['locale' => app()->getLocale()])
                ->with('error', __('storefront.auth.google_failed'));
        }

        $user = User::query()
            ->where('provider_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if (! $user) {
            $user = new User();
            $user->email = $googleUser->getEmail();
            $user->password = Hash::make(Str::random(40));
            $user->preferred_language = app()->getLocale();
            $user->is_active = true;
            $user->is_admin = false;
        }

        $user->forceFill([
            'name' => filled($user->name) ? $user->name : ($googleUser->getName() ?: $googleUser->getNickname() ?: 'Google User'),
            'email' => $googleUser->getEmail() ?: $user->email,
            'email_verified_at' => $user->email_verified_at ?: now(),
            'email_verification_code' => null,
            'email_verification_expires_at' => null,
            'auth_provider' => 'google',
            'provider_id' => $googleUser->getId(),
            'avatar_url' => $googleUser->getAvatar(),
            'preferred_language' => $user->preferred_language ?: app()->getLocale(),
        ])->save();

        if (! $user->is_active) {
            return redirect()->route('login', ['locale' => app()->getLocale()])
                ->withErrors(['email' => __('storefront.auth.account_inactive')]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();
        $request->session()->forget('google_auth_locale');

        return $this->completeAuthentication(
            $request,
            $user,
            __('storefront.auth.google_welcome'),
        );
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

    private function completeAuthentication(Request $request, ?User $user, string $message): RedirectResponse
    {
        $user?->forceFill(['last_login_at' => now()])->save();

        if ($user?->is_admin) {
            return redirect()->route('admin.dashboard', ['locale' => app()->getLocale()])
                ->with('success', $message);
        }

        return redirect()->intended(route('store.dashboard', ['locale' => app()->getLocale()]))
            ->with('success', $message);
    }

    private function rememberIntendedUrl(Request $request): void
    {
        $target = trim((string) $request->input('redirect_to', ''));

        if ($target !== '' && $this->isSafeRedirectTarget($request, $target)) {
            $request->session()->put('url.intended', $target);
        }
    }

    private function isSafeRedirectTarget(Request $request, string $target): bool
    {
        if (str_starts_with($target, '/')) {
            return true;
        }

        $targetParts = parse_url($target);
        if (! is_array($targetParts)) {
            return false;
        }

        $targetHost = $targetParts['host'] ?? null;
        $targetScheme = $targetParts['scheme'] ?? null;
        $allowedHosts = array_filter([
            $request->getHost(),
            parse_url((string) config('app.url'), PHP_URL_HOST),
        ]);

        return in_array($targetScheme, ['http', 'https'], true)
            && $targetHost !== null
            && in_array($targetHost, $allowedHosts, true);
    }

    private function googleIsConfigured(): bool
    {
        return class_exists(Socialite::class)
            && filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'))
            && filled(config('services.google.redirect'));
    }
}
