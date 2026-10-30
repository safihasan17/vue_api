<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

/**
 * Token (Sanctum) authentication for the Vue SPA.
 * The Blade/session login (routes/auth.php) is untouched.
 */
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone'    => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Same defaults as the Blade RegisteredUserController (role_id 2).
        // Change role_id here if self-registered users should get another role.
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role_id'  => 2,
            'phone'    => $request->phone ?: '015558584',
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['success' => 'Registration successful. Please sign in.'], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = Str::transliterate(Str::lower($request->string('email')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($key, 5)) {
            event(new Lockout($request));
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)]),
            ]);
        }

        $user = User::with('role')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key);

            throw ValidationException::withMessages(['email' => trans('auth.failed')]);
        }

        // `active` column: false / 0 = disabled account
        if (isset($user->active) && !$user->active) {
            throw ValidationException::withMessages(['email' => 'This account is inactive.']);
        }

        RateLimiter::clear($key);

        return response()->json([
            'success' => true,
            'token'   => $user->createToken('spa')->plainTextToken,
            'user'    => $user,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json(['success' => true, 'user' => $request->user()->load('role')]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['success' => true, 'message' => 'Logged out']);
    }
}
