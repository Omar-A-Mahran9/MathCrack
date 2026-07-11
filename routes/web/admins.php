<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Back\Admins\Roles\RolesController;
use App\Http\Controllers\Web\Back\Admins\Settings\SettingsController;
use App\Http\Controllers\Web\Back\Admins\Settings\LanguagesController;
use App\Http\Controllers\Web\Back\Admins\Settings\CurrenciesController;
use App\Http\Controllers\Web\Back\Admins\Settings\TaxesController;
use App\Http\Controllers\Web\Back\Admins\Settings\PaymentsController;
use App\Http\Controllers\Web\Back\Admins\Settings\FirewallsController;
use App\Http\Controllers\Web\Back\Admins\Settings\BackupAndUpdateController;
use App\Http\Controllers\Web\Back\Admins\Settings\SeoController;
use App\Http\Controllers\Web\Back\Admins\Users\UsersController;
use App\Http\Controllers\Web\Back\Admins\Statistics\VisitiorsController;
use App\Http\Controllers\Web\Back\Admins\Customers\CustomersController;
use App\Http\Controllers\Web\Back\Admins\Tickets\TicketsController;
use App\Http\Controllers\Web\Back\Admins\Notes\NotesController;
use App\Http\Controllers\Web\Back\Admins\Subscribers\SubscribersController;
use App\Http\Controllers\Web\Back\Admins\Tasks\TasksController;
use App\Http\Controllers\Web\Back\Admins\Chats\ChatsController;
use App\Http\Controllers\Web\Back\Admins\Pages\Blogs\BlogCategoriesController;
use App\Http\Controllers\Web\Back\Admins\Pages\Blogs\BlogsController;
use App\Http\Controllers\Web\Back\Admins\Pages\Questions\QuestionsController;
use App\Http\Controllers\Web\Back\Admins\Pages\Contacts\ContactsController;
use App\Http\Controllers\Web\Back\Admins\Pages\Teams\TeamsController;
use App\Http\Controllers\Web\Back\Admins\Pages\PagesController;
use App\Http\Controllers\Web\Back\Admins\Levels\LevelsController;
use App\Http\Controllers\Web\Back\Admins\Courses\CoursesController;
use App\Http\Controllers\Web\Back\Admins\Lectures\LatexAssignmentImportController;
use App\Http\Controllers\Web\Back\Admins\Lectures\LecturesController;
use App\Http\Controllers\Web\Back\Admins\Lives\LivesController;
use App\Http\Controllers\Web\Back\Admins\Tests\LatexTestImportController;
use App\Http\Controllers\Web\Back\Admins\Tests\TestsController;
use App\Http\Controllers\Web\Back\Admins\Tests\TestQuestionsController;

// ==================================================admin routes===============================================================================================================

    // --------------------------------------------------invoices-------------------------------------------------------------------------------------------------------------
    Route::prefix('admins/invoices')->controller(\App\Http\Controllers\Web\Back\Admins\Invoices\InvoicesController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard.admins.invoices');
        Route::post('/store', 'store')->name('dashboard.admins.invoices-store');
        Route::delete('/delete', 'delete')->name('dashboard.admins.invoices-delete');
        Route::delete('/delete-selected', 'deleteSelected')->name('dashboard.admins.invoices-delete-selected');
        Route::get('/show', 'show')->name('dashboard.admins.invoices-show');
        Route::get('/student-invoices', 'studentInvoices')->name('dashboard.admins.invoices-student');
        Route::get('/get-students', 'getStudents')->name('dashboard.admins.invoices-get-students');
        Route::get('/get-items', 'getItems')->name('dashboard.admins.invoices-get-items');
    });
    // --------------------------------------------------lectures-------------------------------------------------------------------------------------------------------------
    Route::prefix('admins/lectures')->controller(LecturesController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard.admins.lectures');
        Route::post('/store', 'store')->name('dashboard.admins.lectures-store');
        Route::get('/edit', 'edit')->name('dashboard.admins.lectures-edit');
        Route::patch('/update', 'update')->name('dashboard.admins.lectures-update');
        Route::delete('/delete', 'delete')->name('dashboard.admins.lectures-delete');
        // Ø§Ù„ÙˆØ§Ø¬Ø¨Ø§Øª
        Route::get('/assignments', 'assignments')->name('dashboard.admins.lectures-assignments');
        Route::post('/assignments/store', 'storeAssignment')->name('dashboard.admins.lectures-assignments-store');
        Route::get('/assignments/edit', 'editAssignment')->name('dashboard.admins.lectures-assignments-edit');
        Route::patch('/assignments/update', 'updateAssignment')->name('dashboard.admins.lectures-assignments-update');
        Route::delete('/assignments/delete', 'deleteAssignment')->name('dashboard.admins.lectures-assignments-delete');
        Route::get('/assignments/preview', 'previewAssignment')->name('dashboard.admins.lectures-assignments-preview');
        // Ø§Ù„Ø£Ø³Ø¦Ù„Ø©
        Route::get('/questions', 'questions')->name('dashboard.admins.lectures-questions');
        Route::post('/questions/store', 'storeQuestion')->name('dashboard.admins.lectures-questions-store');
        Route::get('/questions/edit', 'editQuestion')->name('dashboard.admins.lectures-questions-edit');
        Route::patch('/questions/update', 'updateQuestion')->name('dashboard.admins.lectures-questions-update');
        Route::post('/questions/delete', 'deleteQuestion')->name('dashboard.admins.lectures-questions-delete');
    });
    Route::prefix('admins/lectures/assignments/latex-import')->controller(LatexAssignmentImportController::class)->group(function () {
        Route::get('/', 'create')->name('dashboard.admins.lectures-assignments-latex-import');
        Route::post('/preview', 'preview')->name('dashboard.admins.lectures-assignments-latex-import-preview');
        Route::post('/store', 'store')->name('dashboard.admins.lectures-assignments-latex-import-store');
    });
    // --------------------------------------------------lives-------------------------------------------------------------------------------------------------------------
    Route::prefix('admins/lives')->controller(LivesController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard.admins.lives');
        Route::post('/store', 'store')->name('dashboard.admins.lives-store');
        Route::get('/edit', 'edit')->name('dashboard.admins.lives-edit');
        Route::patch('/update', 'update')->name('dashboard.admins.lives-update');
        Route::get('/delete', 'delete')->name('dashboard.admins.lives-delete');
    });
    // --------------------------------------------------levels-------------------------------------------------------------------------------------------------------------
    Route::prefix('admins/levels')->controller(LevelsController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard.admins.levels');
        Route::post('/store', 'store')->name('dashboard.admins.levels-store');
        Route::patch('/update', 'update')->name('dashboard.admins.levels-update');
        Route::delete('/delete', 'delete')->name('dashboard.admins.levels-delete');
        Route::get('/students', 'students')->name('dashboard.admins.levels-students');
        Route::get('/courses', 'courses')->name('dashboard.admins.levels-courses');
    });
    // --------------------------------------------------courses-------------------------------------------------------------------------------------------------------------
    Route::prefix('admins/courses')->controller(CoursesController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard.admins.courses');
        Route::post('/store', 'store')->name('dashboard.admins.courses-store');
        Route::get('/edit', 'edit')->name('dashboard.admins.courses-edit');
        Route::patch('/update', 'update')->name('dashboard.admins.courses-update');
        Route::delete('/delete', 'delete')->name('dashboard.admins.courses-delete');
    });
    // 
// --------------------------------------------------books-------------------------------------------------------------------------------------------------------------
Route::prefix('admins/books')
    ->controller(\App\Http\Controllers\Web\Back\Admins\Books\BooksController::class)
    ->group(function () {
        Route::get('/', 'index')->name('dashboard.admins.books');
        Route::post('/store', 'store')->name('dashboard.admins.books-store');

        Route::get('/edit', 'edit')->name('dashboard.admins.books-edit');
        Route::post('/update', 'update')->name('dashboard.admins.books-update');

        Route::post('/convert', 'convert')->name('dashboard.admins.books-convert');

        Route::delete('/delete', 'delete')->name('dashboard.admins.books-delete');
    });    // =======================================================customers==============================================================================================================
    Route::prefix('admins/students')->controller(CustomersController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard.admins.customers');
        Route::get('/search', 'search')->name('dashboard.admins.customers-search');
        Route::get('/show', 'show')->name('dashboard.admins.customers-show');
        Route::get('/add', 'add')->name('dashboard.admins.customers-add');
        Route::post('/store', 'store')->name('dashboard.admins.customers-store');
        Route::get('/edit', 'edit')->name('dashboard.admins.customers-edit');
        Route::patch('/update', 'update')->name('dashboard.admins.customers-update');
        Route::put('/updatepassword', 'updatepassword')->name('dashboard.admins.customers-updatepassword');
        Route::patch('/inactive', 'inactive')->name('dashboard.admins.customers-inactive');
        Route::patch('/active', 'active')->name('dashboard.admins.customers-active');
        Route::delete('/delete-inactive', 'deleteinactive')->name('dashboard.admins.customers-delete-inactive');
        Route::delete('/delete-all-inactive', 'deleteallinactive')->name('dashboard.admins.customers-delete-allinactive');
        Route::get('/export', 'export')->name('dashboard.admins.customers.export');
        Route::delete('/delete-selected', 'deleteSelected')->name('dashboard.admins.customers-delete-selected');
        Route::get('/import', 'import')->name('dashboard.admins.customers-import-get');
        Route::post('/import', 'import')->name('dashboard.admins.customers-import-post');
    });
    // =======================================================tickets==============================================================================================================
    Route::prefix('admins/tickets')->controller(TicketsController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard.admins.tickets');
        Route::post('/store', 'store')->name('dashboard.admins.tickets-store');
        Route::get('/show', 'show')->name('dashboard.admins.tickets-show');
        Route::post('/reply', 'reply')->name('dashboard.admins.tickets-reply');
        Route::get('/close', 'close')->name('dashboard.admins.tickets-close');
        Route::get('/active', 'active')->name('dashboard.admins.tickets-active');
        Route::get('/delete', 'delete')->name('dashboard.admins.tickets-delete');
        Route::get('/deleteAll', 'deleteAll')->name('dashboard.admins.tickets-deleteAll');
        Route::get('/delete-selected', 'deleteSelected')->name('dashboard.admins.tickets-deleteSelected');
        Route::get('/get-new-messages', 'getNewMessages')->name('dashboard.admins.tickets-get-new-messages');
    });
    // =======================================================subscribers==============================================================================================================
    Route::prefix('admins/subscribers')->controller(SubscribersController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard.admins.subscribers');
        Route::post('/store', 'store')->name('dashboard.admins.subscribers-store');
        Route::get('/edit', 'edit')->name('dashboard.admins.subscribers-edit');
        Route::patch('/update', 'update')->name('dashboard.admins.subscribers-update');
        Route::get('/delete', 'delete')->name('dashboard.admins.subscribers-delete');
        Route::get('/delete-selected', 'deleteSelected')->name('dashboard.admins.subscribers-deleteSelected');
    });
    // =======================================================notes==============================================================================================================
    Route::prefix('admins/notes')->controller(NotesController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard.admins.notes');
        Route::get('/show', 'show')->name('dashboard.admins.notes-show');
        Route::post('/store', 'store')->name('dashboard.admins.notes-store');
        Route::get('/edit', 'edit')->name('dashboard.admins.notes-edit');
        Route::patch('/update', 'update')->name('dashboard.admins.notes-update');
        Route::delete('/delete', 'delete')->name('dashboard.admins.notes-delete');
        Route::delete('/delete-selected', 'deleteSelected')->name('dashboard.admins.notes-deleteSelected');
        Route::get('/check', 'check')->name('dashboard.admins.notes-check');
    });
    // =======================================================blog==============================================================================================================
        Route::prefix('admins/blog/articles')->controller(BlogsController::class)->group(function () {
            Route::get('/', 'index')->name('dashboard.admins.blogs.articles');
            Route::get('/show', 'show')->name('dashboard.admins.blogs.articles-show');
            Route::post('/store', 'store')->name('dashboard.admins.blogs.articles-store');
            Route::get('/get-translations', 'getTranslations')->name('dashboard.admins.blogs.articles-get-translations');
            Route::patch('/translate', 'translate')->name('dashboard.admins.blogs.articles-translate');
            Route::get('/auto-translate', 'autoTranslate')->name('dashboard.admins.blogs.articles-auto-translate');
            Route::get('/edit', 'edit')->name('dashboard.admins.blogs.articles-edit');
            Route::patch('/update', 'update')->name('dashboard.admins.blogs.articles-update');
            Route::get('/delete', 'delete')->name('dashboard.admins.blogs.articles-delete');
            Route::get('/delete-selected', 'deleteSelected')->name('dashboard.admins.blogs.articles-deleteSelected');
            Route::get('/comments/delete', 'deleteComment')->name('dashboard.admins.blogs.comments-delete');
        });
        Route::prefix('admins/blog/categories')->controller(BlogCategoriesController::class)->group(function () {
            Route::get('/', 'index')->name('dashboard.admins.blogs.categories');
            Route::post('/store', 'store')->name('dashboard.admins.blogs.categories-store');
            Route::get('/get-translations', 'getTranslations')->name('dashboard.admins.blogs.categories-get-translations');
            Route::patch('/translate', 'translate')->name('dashboard.admins.blogs.categories-translate');
            Route::get('/auto-translate', 'autoTranslate')->name('dashboard.admins.blogs.categories-auto-translate');
            Route::get('/edit', 'edit')->name('dashboard.admins.blogs.categories-edit');
            Route::patch('/update', 'update')->name('dashboard.admins.blogs.categories-update');
            Route::get('/delete', 'delete')->name('dashboard.admins.blogs.categories-delete');
            Route::get('/delete-selected', 'deleteSelected')->name('dashboard.admins.blogs.categories-deleteSelected');
        });
    // =======================================================pages==============================================================================================================
        Route::prefix('admins/pages')->controller(PagesController::class)->group(function () {
            Route::patch('/update', 'update')->name('dashboard.admins.pages-update');
            Route::get('/get-translations', 'getTranslations')->name('dashboard.admins.pages-get-translations');
            Route::patch('/translate', 'translate')->name('dashboard.admins.pages-translate');
            Route::get('/auto-translate', 'autoTranslate')->name('dashboard.admins.pages-auto-translate');
            Route::get('/{page}', 'index')->name('dashboard.admins.pages');
        });
        // --------------------------------------------------questions-------------------------------------------------------------------------------------------------------------
        Route::prefix('admins/questions')->controller(QuestionsController::class)->group(function () {
            Route::get('/', 'index')->name('dashboard.admins.questions');
            Route::post('/store', 'store')->name('dashboard.admins.questions-store');
            Route::get('/edit', 'edit')->name('dashboard.admins.questions-edit');
            Route::patch('/update', 'update')->name('dashboard.admins.questions-update');
            Route::get('/delete', 'delete')->name('dashboard.admins.questions-delete');
            Route::get('/delete-selected', 'deleteSelected')->name('dashboard.admins.questions-deleteSelected');
            Route::get('/get-translations', 'getTranslations')->name('dashboard.admins.questions-get-translations');
            Route::patch('/translate', 'translate')->name('dashboard.admins.questions-translate');
            Route::get('/auto-translate', 'autoTranslate')->name('dashboard.admins.questions-auto-translate');
        });
        // --------------------------------------------------teams-------------------------------------------------------------------------------------------------------------
        Route::prefix('admins/teams')->controller(TeamsController::class)->group(function () {
            Route::get('/', 'index')->name('dashboard.admins.teams');
            Route::post('/store', 'store')->name('dashboard.admins.teams-store');
            Route::get('/edit', 'edit')->name('dashboard.admins.teams-edit');
            Route::patch('/update', 'update')->name('dashboard.admins.teams-update');
            Route::get('/delete', 'delete')->name('dashboard.admins.teams-delete');
        });
        // --------------------------------------------------Contact us-------------------------------------------------------------------------------------------------------------
        Route::prefix('admins/contact-us')->controller(ContactsController::class)->group(function () {
            Route::get('/', 'index')->name('dashboard.admins.contacts');
            Route::get('/show', 'show')->name('dashboard.admins.contacts-show');
            Route::get('/done', 'done')->name('dashboard.admins.contacts-done');
            Route::get('/delete', 'delete')->name('dashboard.admins.contacts-delete');
            Route::get('/delete-selected', 'deleteSelected')->name('dashboard.admins.contacts-deleteSelected');
        });
    // =======================================================users==============================================================================================================
    Route::prefix('admins/users')->controller(UsersController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard.admins.users');
        Route::get('/show', 'show')->name('dashboard.admins.users-show');
        Route::get('/export', 'export')->name('dashboard.admins.users.export');
        Route::get('/add', 'add')->name('dashboard.admins.users-add');
        Route::post('/store', 'store')->name('dashboard.admins.users-store');
        Route::get('/edit', 'edit')->name('dashboard.admins.users-edit');
        Route::patch('/update', 'update')->name('dashboard.admins.users-update');
        Route::put('/updatepassword', 'updatepassword')->name('dashboard.admins.users-updatepassword');
        Route::patch('/inactive', 'inactive')->name('dashboard.admins.users-inactive');
        Route::patch('/active', 'active')->name('dashboard.admins.users-active');
        Route::delete('/delete-inactive', 'deleteinactive')->name('dashboard.admins.users-delete-inactive');
        Route::delete('/delete-all-inactive', 'deleteallinactive')->name('dashboard.admins.users-delete-allinactive');
        Route::post('/role', 'role')->name('dashboard.admins.users-role');
        Route::post('/roledelete', 'roledelete')->name('dashboard.admins.users-roledelete');
        Route::delete('/delete-selected', 'deleteSelected')->name('dashboard.admins.users-delete-selected');
        Route::get('/import', 'import')->name('dashboard.admins.users-import-get');
        Route::post('/import', 'import')->name('dashboard.admins.users-import-post');
    });
    // =======================================================tasks==============================================================================================================
    Route::prefix('admins/tasks')->controller(TasksController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard.admins.tasks');
        Route::post('/store', 'store')->name('dashboard.admins.tasks-store');
        Route::get('/edit', 'edit')->name('dashboard.admins.tasks-edit');
        Route::patch('/update', 'update')->name('dashboard.admins.tasks-update');
        Route::get('/delete', 'delete')->name('dashboard.admins.tasks-delete');
        Route::get('/delete-selected', 'deleteSelected')->name('dashboard.admins.tasks-delete-selected');
        Route::post('/update-status', 'updateStatus')->name('dashboard.admins.tasks-update-status');
        Route::post('/check-overdue', 'checkOverdueTasks')->name('dashboard.admins.tasks-check-overdue');
        Route::get('/calendar', 'calendar')->name('dashboard.admins.tasks-calendar');
    });
    // =======================================================chats==============================================================================================================
    Route::prefix('admins/chats')->controller(ChatsController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard.admins.chats');
        Route::get('/show', 'show')->name('dashboard.admins.chats.show');
        Route::post('/store', 'store')->name('dashboard.admins.chats.store');
        Route::post('/send-message', 'sendMessage')->name('dashboard.admins.chats.send-message');
        Route::post('/add-users-to-group', 'addUsersToGroup')->name('dashboard.admins.chats.add-users-to-group');
        Route::post('/remove-user-from-group', 'removeUserFromGroup')->name('dashboard.admins.chats.remove-user-from-group');
        Route::get('/leave-group', 'leaveGroup')->name('dashboard.admins.chats.leave-group');
        Route::get('/get-new-messages', 'getNewMessages')->name('dashboard.admins.chats.get-new-messages');
        Route::get('/search-users', 'searchUsers')->name('dashboard.admins.chats.search-users');
        Route::get('/check-read-status', 'checkReadStatus')->name('dashboard.admins.chats.check-read-status');
    });
    // =======================================================roles & permitions==============================================================================================================
    Route::prefix('admins/roles')->controller(RolesController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard.admins.roles');
        Route::post('/store', 'store')->name('dashboard.admins.roles-store');
        Route::get('/edit', 'edit')->name('dashboard.admins.roles-edit');
        Route::post('/update', 'update')->name('dashboard.admins.roles-update');
        Route::get('/delete', 'delete')->name('dashboard.admins.roles-delete');
    });
    // =======================================================statistics==============================================================================================================
    Route::prefix('admins/statistics')->controller(VisitiorsController::class)->group(function () {
        Route::get('/visitors', 'visitors')->name('dashboard.admins.statistics-visitors');
        Route::post('/visitors/status', 'visitorsStatus')->name('dashboard.admins.statistics-visitors-status');
        Route::get('/google', 'google')->name('dashboard.admins.statistics-google');
        Route::post('/google/status', 'googleStatus')->name('dashboard.admins.statistics-google-status');
    });
    // =======================================================Settings===============================================================================================================
        Route::prefix('admins/settings')->controller(SettingsController::class)->group(function () {
            Route::get('/', 'index')->name('dashboard.admins.settings');
            Route::post('/update', 'update')->name('dashboard.admins.settings-update');
            Route::post('/clear-cache', 'clearCache')->name('dashboard.admins.clear-cache');
            Route::post('/reset', 'reset')->name('dashboard.admins.settings-reset');
            Route::get('/themes/{theme}', 'themes')->name('dashboard.admins.settings-themes');
        });
        // --------------------------------------------------languages-------------------------------------------------------------------------------------------------------------
        Route::prefix('admins/settings/languages')->controller(LanguagesController::class)->group(function () {
            Route::get('/', 'index')->name('dashboard.admins.languages');
            Route::get('/status', 'status')->name('dashboard.admins.languages-status');
            Route::get('/translate', 'translate')->name('dashboard.admins.languages-translate');
            Route::post('/translate/store', 'translateStore')->name('dashboard.admins.languages-translate-store');
            Route::get('/delete', 'delete')->name('dashboard.admins.languages-delete');
        });
        // --------------------------------------------------seo-------------------------------------------------------------------------------------------------------------
        Route::prefix('admins/settings/seo')->controller(SeoController::class)->group(function () {
            Route::get('/', 'index')->name('dashboard.admins.seo');
            Route::get('/show', 'show')->name('dashboard.admins.seo-show');
            Route::get('/edit', 'edit')->name('dashboard.admins.seo-edit');
            Route::patch('/update', 'update')->name('dashboard.admins.seo-update');
            Route::get('/get-translations', 'getTranslations')->name('dashboard.admins.seo-get-translations');
            Route::patch('/translate', 'translate')->name('dashboard.admins.seo-translate');
            Route::get('/auto-translate', 'autoTranslate')->name('dashboard.admins.seo-auto-translate');
        });
        // --------------------------------------------------currencies-------------------------------------------------------------------------------------------------------------
        Route::prefix('admins/settings/currencies')->controller(CurrenciesController::class)->group(function () {
            Route::get('/', 'index')->name('dashboard.admins.currencies');
            Route::get('/exchange', 'exchange')->name('dashboard.admins.currencies-exchange');
            Route::get('/status', 'status')->name('dashboard.admins.currencies-status');
            Route::get('/edit', 'edit')->name('dashboard.admins.currencies-edit');
            Route::patch('/update', 'update')->name('dashboard.admins.currencies-update');
            Route::get('/delete', 'delete')->name('dashboard.admins.currencies-delete');
        });
        // --------------------------------------------------payments-------------------------------------------------------------------------------------------------------------
        Route::prefix('admins/settings/payments')->controller(PaymentsController::class)->group(function () {
            Route::get('/', 'index')->name('dashboard.admins.payments');
            Route::post('/update', 'update')->name('dashboard.admins.payments-update');
            Route::post('/translate', 'translate')->name('dashboard.admins.payments-translate');
        });
        // --------------------------------------------------taxes-------------------------------------------------------------------------------------------------------------
        Route::prefix('admins/settings/taxes')->controller(TaxesController::class)->group(function () {
            Route::get('/', 'index')->name(name: 'dashboard.admins.taxes');
            Route::post('/store', 'store')->name('dashboard.admins.taxes-store');
            Route::get('/edit', 'edit')->name('dashboard.admins.taxes-edit');
            Route::patch('/update', 'update')->name('dashboard.admins.taxes-update');
            Route::get('/get-translations', 'getTranslations')->name('dashboard.admins.taxes-get-translations');
            Route::patch('/translate', 'translate')->name('dashboard.admins.taxes-translate');
            Route::get('/auto-translate', 'autoTranslate')->name('dashboard.admins.taxes-auto-translate');
            Route::get('/delete', 'delete')->name('dashboard.admins.taxes-delete');
        });
        // --------------------------------------------------firewalls-------------------------------------------------------------------------------------------------------------
        Route::prefix('admins/settings/firewalls')->controller(FirewallsController::class)->group(function () {
            Route::get('/', 'index')->name('dashboard.admins.firewalls');
            Route::post('/store', 'store')->name('dashboard.admins.firewalls-store');
            Route::get('/delete', 'delete')->name('dashboard.admins.firewalls-delete');
        });
        // --------------------------------------------------backup&update-------------------------------------------------------------------------------------------------------------
        Route::prefix('admins/settings/backup')->controller(BackupAndUpdateController::class)->group(function () {
            Route::get('/take', 'take')->name('dashboard.admins.backup-take');
            Route::get('/delete', 'delete')->name('dashboard.admins.backup-delete');
        });
        Route::prefix('admins/settings/update')->controller(BackupAndUpdateController::class)->group(function () {
            Route::get('/check', 'checkUpdate')->name('dashboard.admins.update-check');
            Route::get('/run', 'runUpdate')->name('dashboard.admins.update-run');
        });

       
// =======================================================tests==============================================================================================================
// =======================================================tests==============================================================================================================

Route::prefix('admins/tests')->controller(\App\Http\Controllers\Web\Back\Admins\Tests\TestsController::class)->group(function () {

    Route::get('/', 'index')->name('dashboard.admins.tests');
    Route::post('/store', 'store')->name('dashboard.admins.tests-store');

    Route::get('/edit', 'edit')->name('dashboard.admins.tests-edit');
    Route::patch('/update', 'update')->name('dashboard.admins.tests-update');

    Route::delete('/delete', 'delete')->name('dashboard.admins.tests-delete');
    Route::get('/show', 'show')->name('dashboard.admins.tests-show');

    Route::post('/toggle-status', 'toggleStatus')->name('dashboard.admins.tests-toggle-status');

Route::get('/preview', 'preview')->name('dashboard.admins.tests-preview');
    
});

Route::prefix('admins/tests/latex-import')->middleware('can:edit tests')->controller(LatexTestImportController::class)->group(function () {
    Route::get('/', 'create')->name('dashboard.admins.tests-latex-import');
    Route::post('/preview', 'preview')->name('dashboard.admins.tests-latex-import-preview');
    Route::post('/store', 'store')->name('dashboard.admins.tests-latex-import-store');
});

// ØµÙØ­Ø© Ø£Ø³Ø¦Ù„Ø© Ø§Ù„Ø§Ø®ØªØ¨Ø§Ø± Ù†ÙØ³ Ø§Ù„Ø§Ø³ØªØ§ÙŠÙ„ Ø§Ù„Ù‚Ø¯ÙŠÙ… Ø¹Ù†Ø¯Ùƒ
Route::prefix('admins/tests-questions')->controller(\App\Http\Controllers\Web\Back\Admins\Tests\TestQuestionsController::class)->group(function () {

    Route::get('/', 'index')->name('dashboard.admins.tests-questions');

    Route::post('/store', 'store')->name('dashboard.admins.tests-questions-store');

    Route::get('/edit', 'edit')->name('dashboard.admins.tests-questions-edit');
    Route::post('/update', 'update')->name('dashboard.admins.tests-questions-update');

    Route::post('/delete', 'delete')->name('dashboard.admins.tests-questions-delete');

    Route::get('/preview', 'preview')->name('dashboard.admins.tests-questions-preview');
});

    // test questions (ÙŠØ¹ØªÙ…Ø¯ Ø¹Ù„Ù‰ query string test_id Ùˆ id Ù…Ø«Ù„ ÙƒÙˆØ¯Ùƒ Ø§Ù„Ø­Ø§Ù„ÙŠ)
    Route::prefix('questions')->group(function () {

        Route::get('/', [\App\Http\Controllers\Web\Back\Admins\Tests\TestQuestionsController::class, 'index'])
            ->name('dashboard.admins.tests-questions');

        Route::post('/store', [\App\Http\Controllers\Web\Back\Admins\Tests\TestQuestionsController::class, 'store'])
            ->name('dashboard.admins.tests-questions-store');

        Route::get('/edit', [\App\Http\Controllers\Web\Back\Admins\Tests\TestQuestionsController::class, 'edit'])
            ->name('dashboard.admins.tests-questions-edit');

        // POST Ù„Ø£Ù† FormData + ØµÙˆØ±
        Route::post('/update', [\App\Http\Controllers\Web\Back\Admins\Tests\TestQuestionsController::class, 'update'])
            ->name('dashboard.admins.tests-questions-update');

        Route::post('/delete', [\App\Http\Controllers\Web\Back\Admins\Tests\TestQuestionsController::class, 'delete'])
            ->name('dashboard.admins.tests-questions-delete');

        Route::get('/preview/{id}', [\App\Http\Controllers\Web\Back\Admins\Tests\TestQuestionsController::class, 'preview'])
            ->name('dashboard.admins.tests-questions-preview');
    });




// =======================================================test questions==============================================================================================================
Route::prefix('admins/tests-questions')->controller(TestQuestionsController::class)->group(function () {
    Route::get('/', 'index')->name('dashboard.admins.tests-questions');
    Route::post('/store', 'store')->name('dashboard.admins.tests-questions-store');
    Route::patch('/update', 'update')->name('dashboard.admins.tests-questions-update');
});
        // --------------------------------------------------test preview-------------------------------------------------------------------------------------------------------------
        Route::prefix('admins/tests')->controller(\App\Http\Controllers\Web\Back\Admins\Tests\TestsController::class)->group(function () {
        Route::get('/{id}/preview', 'preview')->name('dashboard.admins.tests-preview');
        });
