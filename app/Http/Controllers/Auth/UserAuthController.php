<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAuthController extends Controller
{
    public function create()
    {
        return view('auth.user-login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        try {
            if (! Auth::attempt($credentials, $request->boolean('remember'))) {
                return back()->withInput()->withErrors([
                    'email' => 'Invalid credentials.',
                ]);
            }

            $request->session()->regenerate();

            if (! Auth::user()?->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withInput()->withErrors([
                    'email' => 'Your account is inactive. Contact support.',
                ]);
            }

            if (Auth::user()?->is_admin) {
                return redirect()->route('admin.dashboard');
            }

            // User dashboard will be implemented in frontend later.
            return redirect('/')->with('success', 'Logged in successfully. User frontend dashboard is coming next.');
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

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
}
