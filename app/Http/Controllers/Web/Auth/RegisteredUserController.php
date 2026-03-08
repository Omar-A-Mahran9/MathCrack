<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Setting;
use App\Models\Country;
use App\Models\Level;
use Illuminate\Support\Facades\Http;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class RegisteredUserController extends Controller
{
    public function create(Request $request)
    {
        $countries = Country::all();
        $levels = Level::all();
        $canRegister = Setting::where('option', 'can_any_register')->value('value');

        if ((int) $canRegister !== 1) {
            return redirect()->route('login')->with('error', 'Registration is not allowed');
        }

        $mockFlow = [
            'action'   => (string) $request->query('action', ''),
            'redirect' => (string) $request->query('redirect', ''),
            'test'     => (string) $request->query('test', 'mock'),
        ];

        return view(theme('auth.register'), [
            'countries' => $countries,
            'levels' => $levels,
            'mockFlow' => $mockFlow,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'firstname' => ['required','string','max:30'],
            'lastname' => ['required','string','max:60'],
            'email' => ['required','string','email','max:255','unique:' . User::class],
            'phone' => ['required','string','max:20'],
            'phone_code' => ['required','string','max:6'],
            'level_id' => ['required','exists:levels,id'],
            'password' => ['required','string','min:8','confirmed'],
        ]);

        $phoneRaw = str_replace(' ','',(string)$request->phone);
        $dial = ltrim((string)$request->phone_code,'+');
        $phone = '+' . $dial . $phoneRaw;

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $phone,
            'password' => Hash::make($request->password),
            'level_id' => $request->level_id,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $action = (string)$request->input('action','');
        $redirect = (string)$request->input('redirect','');
        $test = (string)$request->input('test','mock');

        if ($action === 'mock' && $redirect === 'mock-test') {

            $courseId = Course::where('level_id',$request->level_id)
                ->orderBy('id')
                ->value('id');

            $localizedMockUrl = LaravelLocalization::getLocalizedURL(
                app()->getLocale(),
                route('mock-test',[],false),
                [],
                true
            );

            return redirect()->to(
                $localizedMockUrl . '?course=' . $courseId . '&test=' . urlencode($test)
            );
        }

        $localizedHomeUrl = LaravelLocalization::getLocalizedURL(
            app()->getLocale(),
            '/',
            [],
            true
        );

        return redirect()->to($localizedHomeUrl);
    }
}