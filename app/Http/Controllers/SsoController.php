<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SsoController extends Controller
{
    /**
     * Redirect the user to the central Auth Server.
     */
    public function redirect()
    {
        return Socialite::driver('laravelpassport')->redirect();
    }

    /**
     * Handle the callback from the Auth Server.
     */
    public function callback()
    {
        try {
            // Retrieve the user information from the Auth Server
            $ssoUser = Socialite::driver('laravelpassport')->user();
        } catch (\Exception $e) {
            // If something goes wrong (e.g., user rejected the authorization)
            return redirect('/')->withErrors('Authentication failed.');
        }

        // Find the user in App 1's database, or create them if they don't exist
        $user = User::updateOrCreate(
            ['email' => $ssoUser->getEmail()], // Match by email
            [
                'name' => $ssoUser->getName(),
                // You can map other fields here if your Auth Server provides them
            ]
        );

        // Log the user into App 1 locally
        Auth::login($user);

        // Redirect them to your app's dashboard or intended page
        return redirect('/dashboard'); 
    }
}