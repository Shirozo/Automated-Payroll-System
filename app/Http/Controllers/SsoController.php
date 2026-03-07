<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SsoController extends Controller
{
    public function redirect()
    {
        // Removed stateless()
        return Socialite::driver('laravelpassport')->redirect();
    }

    public function callback(Request $request)
    {
        try {
            // Removed stateless()
            $ssoUser = Socialite::driver('laravelpassport')->user();
        } catch (\Exception $e) {
            return;
        }

        $user = User::updateOrCreate(
            ['email' => $ssoUser->getEmail()],
            ['name' => $ssoUser->getName()]
        );

        Auth::login($user);

        return redirect('/dashboard');
    }
}
