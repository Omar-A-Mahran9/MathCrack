<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\Back\Users\Tickets\TicketsController;
use App\Http\Controllers\Web\Back\Users\Notes\NotesController;
use App\Http\Controllers\Web\Back\Users\Courses\CoursesController;
use App\Http\Controllers\Web\Back\Users\Invoices\InvoicesController;
use App\Http\Controllers\Web\Back\Users\Payment\PaymentController;
use App\Http\Controllers\Web\Back\Users\Tests\TestsController;
use App\Http\Controllers\Web\Back\Users\Tests\TestPurchaseController;
use App\Http\Controllers\Web\Back\Users\Lives\LivesController;
use App\Http\Controllers\Web\Back\Users\Books\BooksController as UserBooksController;

/*
|--------------------------------------------------------------------------
| Users Tests
|--------------------------------------------------------------------------
*/
Route::prefix('users/tests')->controller(TestsController::class)->group(function () {
    Route::get('/', 'index')->name('dashboard.users.tests');
    Route::get('/{id}', 'show')->name('dashboard.users.tests.show');
    Route::post('/{id}/start', 'start')->name('dashboard.users.tests.start');
    Route::get('/{id}/take', 'take')->name('dashboard.users.tests.take');
    Route::get('/{id}/break', 'break')->name('dashboard.users.tests.break');
    Route::post('/{id}/start-part2', 'startPart2')->name('dashboard.users.tests.start-part2');
    Route::post('/save-answer', 'saveAnswer')->name('dashboard.users.tests.save-answer');
    Route::post('/ai-explanation', 'aiExplanation')->middleware('throttle:10,1')->name('dashboard.users.tests.ai-explanation');
    Route::post('/{id}/submit-part1', 'submitPart1')->name('dashboard.users.tests.submit-part1');
    Route::post('/{id}/submit-part2', 'submitPart2')->name('dashboard.users.tests.submit-part2');
    Route::get('/{id}/results', 'results')->name('dashboard.users.tests.results');
    Route::get('/{id}/remaining-time', 'getRemainingTime')->name('dashboard.users.tests.remaining-time');
});

/*
|--------------------------------------------------------------------------
| Score Calculator
|--------------------------------------------------------------------------
*/
Route::get('/users/score-calc', function () {
    return view('themes.default.back.users.score-calc');
})->name('dashboard.users.score-calc');

/*
|--------------------------------------------------------------------------
| Test Purchase
|--------------------------------------------------------------------------
*/
Route::prefix('users/tests/purchase')->controller(TestPurchaseController::class)->group(function () {
    Route::get('/test/{id}', 'showTest')->name('dashboard.users.tests.purchase.test');
    Route::get('/course-tests/{id}', 'showCourseTests')->name('dashboard.users.tests.purchase.course-tests');
});

/*
|--------------------------------------------------------------------------
| Invoices
|--------------------------------------------------------------------------
*/
Route::prefix('users/invoices')->controller(InvoicesController::class)->group(function () {
    Route::get('/', 'index')->name('dashboard.users.invoices');
    Route::get('/show', 'show')->name('dashboard.users.invoices-show');
});

/*
|--------------------------------------------------------------------------
| Courses + Assignments
|--------------------------------------------------------------------------
*/
Route::prefix('users/courses')->controller(CoursesController::class)->group(function () {
    Route::get('/', 'index')->name('dashboard.users.courses');
   Route::get('/show', 'show')->name('dashboard.users.courses-lectures');
    Route::get('/lectures/show', 'lectureShow')->name('dashboard.users.courses-lectures-show');
    Route::get('/purchase', 'purchaseCourse')->name('dashboard.users.courses-purchase');
    Route::get('/pay', 'pay')->name('dashboard.users.courses-pay');

    Route::prefix('assignments')->group(function () {
        Route::get('/start', 'startAssignment')->name('dashboard.users.assignments-start');
        Route::get('/take', 'takeAssignment')->name('dashboard.users.assignments-take');
        Route::post('/submit', 'submitAssignment')->name('dashboard.users.assignments-submit');
        Route::post('/save-progress', 'saveAssignmentProgress')->name('dashboard.users.assignments-save-progress');
        Route::post('/ai-explanation', 'assignmentAiExplanation')->middleware('throttle:10,1')->name('dashboard.users.assignments-ai-explanation');
        Route::get('/results', 'assignmentResults')->name('dashboard.users.assignments-results');
    });
});

/*
|--------------------------------------------------------------------------
| Users Books
|--------------------------------------------------------------------------
*/
Route::prefix('users/books')->controller(UserBooksController::class)->group(function () {
    Route::get('/', 'index')->name('dashboard.users.books');

    Route::get('/{book:slug}', 'show')->name('dashboard.users.books-show');

    Route::post('/{book:slug}/purchase', 'purchase')->name('dashboard.users.books-purchase');
});

/*
|--------------------------------------------------------------------------
| Tickets
|--------------------------------------------------------------------------
*/
Route::prefix('users/tickets')->controller(TicketsController::class)->group(function () {
    Route::get('/', 'index')->name('dashboard.users.tickets');
    Route::get('/show', 'show')->name('dashboard.users.tickets-show');
    Route::post('/reply', 'reply')->name('dashboard.users.tickets-reply');
    Route::post('/store', 'store')->name('dashboard.users.tickets-store');
    Route::get('/get-new-messages', 'getNewMessages')->name('dashboard.users.tickets-get-new-messages');
});

/*
|--------------------------------------------------------------------------
| Notes
|--------------------------------------------------------------------------
*/
Route::prefix('users/notes')->controller(NotesController::class)->group(function () {
    Route::get('/', 'index')->name('dashboard.users.notes');
    Route::get('/show', 'show')->name('dashboard.users.notes-show');
    Route::post('/store', 'store')->name('dashboard.users.notes-store');
    Route::get('/edit', 'edit')->name('dashboard.users.notes-edit');
    Route::patch('/update', 'update')->name('dashboard.users.notes-update');
    Route::delete('/delete', 'delete')->name('dashboard.users.notes-delete');
    Route::delete('/delete-selected', 'deleteSelected')->name('dashboard.users.notes-deleteSelected');
    Route::get('/check', 'check')->name('dashboard.users.notes-check');
});

/*
|--------------------------------------------------------------------------
| Payment
|--------------------------------------------------------------------------
*/
Route::prefix('users/payment')->controller(PaymentController::class)->group(function () {
    Route::post('/process', 'processPayment')->name('dashboard.users.process-payment');
    Route::get('/payments/verify/{payment?}', 'verify')->name('verify-payment');
    Route::get('/success', 'paymentSuccess')->name('dashboard.users.payment-success');
    Route::get('/failed', 'paymentFailed')->name('dashboard.users.payment-failed');
    Route::get('/cancelled', 'paymentCancelled')->name('dashboard.users.payment-cancelled');
});

/*
|--------------------------------------------------------------------------
| Lives
|--------------------------------------------------------------------------
*/
Route::prefix('users/lives')->controller(LivesController::class)->group(function () {
    Route::get('/', 'index')->name('dashboard.users.lives');
    Route::get('/show', 'show')->name('dashboard.users.lives-show');
    Route::get('/purchase', 'purchase')->name('dashboard.users.lives-purchase');
});