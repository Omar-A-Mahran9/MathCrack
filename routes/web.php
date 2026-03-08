<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Http\Middleware\EnhancedVisitorTracker;
use App\Http\Middleware\ActiveLocaleMiddleware;

use App\Http\Controllers\Web\Front\FrontController;
use App\Http\Controllers\Web\Back\HomeController;
use App\Http\Controllers\Web\Back\ProfileController;
use App\Http\Controllers\Web\Back\NotificationController;
use App\Http\Controllers\Web\TestComparisonController;

use App\Http\Controllers\Web\Back\Admins\Tests\TestsController as AdminTestsController;
use App\Http\Controllers\Web\Back\Users\Tests\TestsController as UserTestsController;
use App\Http\Controllers\Web\Auth\PasswordResetController;
use Illuminate\Support\Facades\Password;

/*
|--------------------------------------------------------------------------
| Root redirect
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect(LaravelLocalization::getLocalizedURL(app()->getLocale(), '/'));
});

/*
|--------------------------------------------------------------------------
| Auth routes (outside localization)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/web/auth.php';

/*
|--------------------------------------------------------------------------
| Mock Test Redirect (بسيط ومباشر)
|--------------------------------------------------------------------------
*/
Route::get('/start-mock', function() {
    if (!auth()->check()) {
        return redirect()->route('register', ['action' => 'mock', 'redirect' => 'mock-test', 'test' => 'mock']);
    }
    
    // التحقق من وجود Mock للمستخدم - عدل حسب قاعدة بياناتك
    // هذه مجرد أمثلة، جرب واحدة تلو الأخرى
    
    // مثال 1: التحقق من جدول tests
    // $hasMock = \App\Models\Test::where('level_id', auth()->user()->level_id)->exists();
    
    // مثال 2: التحقق من جدول exams  
    // $hasMock = \App\Models\Exam::where('level_id', auth()->user()->level_id)->exists();
    
    // مثال 3: التحقق من جدول courses
    // $hasMock = \App\Models\Course::where('level_id', auth()->user()->level_id)->exists();
    
    // حالياً نستخدم false للتجربة
    $hasMock = false;
    
    if ($hasMock) {
        return redirect('/mock-test?course=1&test=mock');
    }
    
    return view('message', [
        'title' => 'Unable to continue',
        'message' => 'No course found for your selected level.',
        'button' => 'Back to Home',
        'url' => url('/')
    ]);
})->name('start.mock');

/*
|--------------------------------------------------------------------------
| Localized Routes
|--------------------------------------------------------------------------
*/
Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [
            'localeSessionRedirect',
            'localizationRedirect',
            'localeViewPath',
            ActiveLocaleMiddleware::class,
            'firewall.ip',
        ],
    ],
    function () {

        Route::middleware([EnhancedVisitorTracker::class])->group(function () {

            Route::controller(FrontController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/about', 'about')->name('about');
                Route::get('/blog', 'blog')->name('blog');
                Route::get('/blog/{slug}', 'blogDetails')->name('blog.show');
                Route::post('/blog/reply', 'blogReply')->name('blog.reply');
                Route::get('/team', 'team')->name('team');
                Route::get('/faqs', 'faqs')->name('faqs');
                Route::get('/contact-us', 'contact')->name('contact');
                Route::post('/contact-us', 'contactStore')->name('contact.store');
                Route::get('/terms', 'terms')->name('terms');
                Route::get('/privacy', 'privacy')->name('privacy');
                Route::post('/subscribe', 'subscribe')->name('subscribe');
                Route::get('/unsubscribe/{token}', 'unsubscribe')->name('unsubscribe');
                Route::post('/kashier-webhook', 'kashierWebhook')->name('kashier-webhook');
                Route::get('/sitemap', 'sitemap')->name('sitemap');
                Route::post('/license-verify', 'licenseVerify')->name('license-verify');
            });

        });

        /*
        |--------------------------------------------------------------------------
        | Dashboard routes
        |--------------------------------------------------------------------------
        */
        Route::middleware(['auth'])->group(function () {

            Route::get('/home', [HomeController::class, 'index'])->name('home');
            Route::get('/mock-test', [UserTestsController::class, 'mockTest'])->name('mock-test');
            Route::prefix('/notification')->controller(NotificationController::class)->group(function () {
                Route::get('/show', 'show')->name('dashboard.notification-show');
                Route::get('/delete', 'delete')->name('dashboard.notification-delete');
                Route::get('/deleteall', 'deleteall')->name('dashboard.notification-deleteAll');
                Route::get('/markall', 'markall')->name('dashboard.notification-markAll');
                Route::get('/sse', 'sse')->name('dashboard.notification-sse');
            });

            Route::prefix('/profile')->controller(ProfileController::class)->group(function () {
                Route::get('/', 'index')->name('dashboard.profile');
                Route::patch('/update', 'update')->name('dashboard.profile-update');
                Route::post('/photo', 'uploadPhoto')->name('dashboard.profile-uploadPhoto');
                Route::put('/password', 'updatePassword')->name('dashboard.profile-updatePassword');
                Route::delete('/delete', 'delete')->name('dashboard.profile-delete');
                Route::post('/apiCreate', 'apiCreate')->name('dashboard.profile-apiCreate');
                Route::get('/apiDelete', 'apiDelete')->name('dashboard.profile-apiDelete');
                Route::get('/2fa', 'show2faForm')->name('profile.2fa.form');
                Route::post('/2fa/enable', 'enable2fa')->name('profile.2fa.enable');
                Route::post('/2fa/disable', 'disable2fa')->name('profile.2fa.disable');
            });

            Route::prefix('dashboard/admins/tests')
                ->name('dashboard.admins.tests.')
                ->controller(AdminTestsController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::post('/store', 'store')->name('store');
                    Route::get('/edit', 'edit')->name('edit');
                    Route::post('/update', 'update')->name('update');
                    Route::get('/show', 'show')->name('show');
                    Route::get('/delete', 'delete')->name('delete');
                    Route::post('/toggle-status', 'toggleStatus')->name('toggle-status');
                    Route::get('/preview/{id}', 'preview')->name('preview');
                });

            Route::prefix('dashboard/users/tests')->name('dashboard.users.tests.')->group(function () {

                Route::get('/', [UserTestsController::class, 'index'])->name('index');

                Route::get('/filters/courses', [UserTestsController::class, 'getCoursesByLevel'])
                    ->name('filters.courses');

                Route::get('/{id}', [UserTestsController::class, 'show'])->name('show');
                Route::get('/{id}/results', [UserTestsController::class, 'results'])->name('results');
                Route::get('/{id}/comparison', [TestComparisonController::class, 'show'])->name('comparison');

                Route::post('/{id}/start', [UserTestsController::class, 'start'])->name('start');
                Route::get('/{id}/take', [UserTestsController::class, 'take'])->name('take');
                Route::get('/{id}/break', [UserTestsController::class, 'break'])->name('break');
                Route::post('/{id}/submit', [UserTestsController::class, 'submit'])->name('submit');

                Route::post('/save-answer', [UserTestsController::class, 'saveAnswer'])->name('save-answer');

                Route::get('/{id}/remaining-time', [UserTestsController::class, 'getRemainingTime'])->name('remaining-time');
                Route::post('/{id}/update-timer', [UserTestsController::class, 'updateTimer'])->name('update-timer');
            });

            require __DIR__ . '/web/users.php';
            require __DIR__ . '/web/admins.php';
        });

        Route::impersonate();
    }
);

/*
|--------------------------------------------------------------------------
| Outside localization
|--------------------------------------------------------------------------
*/
Route::get('/admins/tests/{id}/print-results', [AdminTestsController::class, 'printResults'])
    ->name('dashboard.admins.tests-results-print');

Route::get('forgot-password', [PasswordResetController::class, 'showForgot'])
    ->middleware('guest')
    ->name('password.request');

Route::post('forgot-password', [PasswordResetController::class, 'sendReset'])
    ->middleware('guest')
    ->name('password.email');

Route::get('reset-password/{token}', [PasswordResetController::class, 'showReset'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('reset-password', [PasswordResetController::class, 'resetPassword'])
    ->middleware('guest')
    ->name('password.update');





Route::get('/mock-unavailable', function() {
    return view('message', [
        'message' => 'No course found for your selected level.'
    ]);
});