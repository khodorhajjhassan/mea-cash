<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'preferred_language' => ['nullable', 'in:ar,en'],
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

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('store.dashboard')
                ->with('success', __('storefront.auth.welcome'));
        } catch (\Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', __('storefront.auth.register_failed'));
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('store.home')
            ->with('success', __('storefront.auth.logged_out'));
    }
}
