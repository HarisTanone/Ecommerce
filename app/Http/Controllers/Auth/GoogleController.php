<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return response()->json([
            'redirect_url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl(),
        ]);
    }
    public function handleGoogleCallback(){
        try {
            // Mendapatkan pengguna dari Google
            $googleUser = Socialite::driver('google')->user();

            // Cek apakah pengguna sudah terdaftar
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                Auth::login($existingUser);
            } else {
                // Jika pengguna baru, buat akun baru
                $newUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt(Str::random(24)), // Password acak
                ]);
                Auth::login($newUser);
            }

            return redirect()->intended('dashboard');
        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getTraceAsString());
            return redirect('login')->with('error', 'Google authentication failed');
        }
    }
}
