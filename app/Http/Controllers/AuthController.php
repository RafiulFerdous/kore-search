<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Cache\RateLimiter;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    protected function rateLimitKey(Request $request): string
    {
        return 'login|' . strtolower($request->email) . '|' . $request->ip();
    }

    public function login(Request $request, RateLimiter $limiter)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $key = $this->rateLimitKey($request);

        if ($limiter->tooManyAttempts($key, 5)) {
            $seconds = $limiter->availableIn($key);
            return redirect()->route('login.throttled', ['seconds' => $seconds]);
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $limiter->clear($key);
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        $limiter->hit($key, 60);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showThrottled(Request $request)
    {
        $seconds = max((int) $request->seconds, 60);
        return view('auth.throttled', ['seconds' => $seconds]);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'student',
            ]);

            $user->assignRole('student');

            DB::commit();

            Auth::login($user);

            return redirect()->route('home');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Registration failed. Please try again.')->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
