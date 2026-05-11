<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(Request $request): View
    {
        if ($request->has('track')) {
            session(['selected_track' => $request->query('track')]);
        }

        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $ip = $request->ip();
        $key = 'login_attempts:' . $ip;

        $maxAttempts = (int) (DB::table('settings')->where('option', 'max_login_attempts')->value('value') ?? 5);
        $maxAttempts = $maxAttempts > 0 ? $maxAttempts : 5;

        $attempts = (int) Cache::get($key . ':attempts', 0);
        $lockoutTime = (int) Cache::get($key . ':lockout_time', 0);

        if ($lockoutTime > time()) {
            $remainingTime = $lockoutTime - time();

            return back()->withErrors([
                'limit' => __('l.Too many login attempts. Please try again in :minutes minutes.', [
                    'minutes' => (int) ceil($remainingTime / 60),
                ]),
            ]);
        }

        try {
            $user = \App\Models\User::where('email', $request->email)->first();

            if (!$user) {
                $phoneInput = $request->email;
                $user = \App\Models\User::where('phone', 'LIKE', '%' . $phoneInput . '%')->first();
            }

            if (!$user) {
                throw new \Exception('Invalid credentials');
            }

            if (!empty($user->google2fa_secret)) {
                session([
                    'auth.2fa.user_id' => $user->id,
                    'auth.2fa.remember' => $request->boolean('remember'),
                ]);

                return redirect()->route('2fa.challenge');
            }

            $request->authenticate();

            Cache::forget($key . ':attempts');
            Cache::forget($key . ':lockout_time');

            if ($request->boolean('remember')) {
                $email = $request->email;
                $password = $request->password;
                $expiry = time() + (60 * 60 * 24 * 30);

                setcookie('remember_user', $email, $expiry, "/");
                setcookie('remember_pass', encrypt($password), $expiry, "/");
            } else {
                setcookie('remember_user', '', time() - 3600, "/");
                setcookie('remember_pass', '', time() - 3600, "/");
            }

            $request->session()->regenerate();

            if (session()->has('selected_track')) {
                $track = session('selected_track');
                session()->forget('selected_track');

                return redirect()->route('dashboard.users.courses', ['track' => $track]);
            }

            return redirect()->intended(route('home', absolute: false));
        } catch (\Exception $e) {
            $newAttempts = $attempts + 1;
            $remainingAttempts = $maxAttempts - $newAttempts;

            Cache::put($key . ':attempts', $newAttempts, now()->addMinutes(10));

            if ($remainingAttempts > 0) {
                return back()->withErrors([
                    'email' => __('l.Invalid credentials. You have :attempts attempts remaining.', [
                        'attempts' => $remainingAttempts,
                    ]),
                ]);
            }

            $lockoutTimeInSeconds = 600;

            if ($newAttempts >= $maxAttempts + 2) {
                $lockoutTimeInSeconds = 3600;
            } elseif ($newAttempts >= $maxAttempts + 1) {
                $lockoutTimeInSeconds = 1800;
            }

            Cache::put(
                $key . ':lockout_time',
                time() + $lockoutTimeInSeconds,
                now()->addSeconds($lockoutTimeInSeconds)
            );

            return back()->withErrors([
                'limit' => __('l.Too many login attempts. Please try again in :minutes minutes.', [
                    'minutes' => (int) ceil($lockoutTimeInSeconds / 60),
                ]),
            ]);
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}