<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function create()
    {
        return view('auth.admin-login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        $credentials['email'] = strtolower(trim((string) $credentials['email']));

        try {
            if (! Auth::attempt($credentials, $request->boolean('remember'))) {
                return back()->withInput()->withErrors([
                    'email' => 'Invalid credentials.',
                ]);
            }

            $request->session()->regenerate();

            if (! Auth::user()?->is_admin) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withInput()->withErrors([
                    'email' => 'You are not authorized to access admin dashboard.',
                ]);
            }

            if (! Auth::user()?->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withInput()->withErrors([
                    'email' => 'Your account is inactive. Contact support.',
                ]);
            }

            return redirect()->route('admin.dashboard')->with('success', 'Welcome back, admin.');
        } catch (\Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Login failed. Please try again.');
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }
}
