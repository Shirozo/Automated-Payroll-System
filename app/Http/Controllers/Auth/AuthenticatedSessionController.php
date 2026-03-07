<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request)
    {
        return inertia('LoginPage', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);

        
        // Only uncomment when using central authentication server
        // if (Auth::user()) {
        //     return redirect()->intended(route('index.dashboard', absolute: false));
        // }

        // if ($request->has('sso_status')) {
        //     if ($request->query('sso_status') === 'true') {
        //         return \Laravel\Socialite\Facades\Socialite::driver('laravelpassport')->redirect();
        //     } else {
        //         return inertia("Auth/Login");
        //     }
        // }

        // $returnUrl = urlencode(url()->current());
        // return redirect("http://192.168.17.234:8000/sso/check?return_to={$returnUrl}");

    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('index.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');

        // Only uncomment when using central authentication server
        // $returnUrl = urlencode(url('/'));
        // return \Inertia\Inertia::location("http://192.168.17.234:8000/logout?redirect={$returnUrl}");
    }
}
