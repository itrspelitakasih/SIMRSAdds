<?php

use App\Http\Controllers\Admin\AppSettingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\DocumentTypeController;
use App\Http\Controllers\Admin\GoogleDriveSettingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WhatsappSettingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Guest\TicketController as GuestTicketController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Models\Document;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public guest routes (no login required)
|--------------------------------------------------------------------------
*/
Route::get('/lapor', [GuestTicketController::class, 'create'])->name('lapor.create');
Route::post('/lapor', [GuestTicketController::class, 'store'])->name('lapor.store');
Route::get('/lapor/sukses/{ticket:ticket_code}', [GuestTicketController::class, 'success'])->name('lapor.success');

Route::get('/lacak', [GuestTicketController::class, 'trackForm'])->name('lacak.form');
Route::post('/lacak', [GuestTicketController::class, 'track'])->name('lacak.lookup');
Route::get('/lacak/{ticket:ticket_code}', [GuestTicketController::class, 'show'])->name('lacak.show');

/*
|--------------------------------------------------------------------------
| Auth routes
|--------------------------------------------------------------------------
*/
Route::get('/signin', [AuthController::class, 'showLogin'])->name('signin');
Route::post('/signin', [AuthController::class, 'login'])->name('signin.attempt');
Route::post('/signout', [AuthController::class, 'logout'])->name('signout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Admin IT routes (login required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/notifications/tickets', [NotificationController::class, 'tickets'])->name('notifications.tickets');

    Route::middleware('permission:tickets')->group(function () {
        Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('tickets.show');
        Route::patch('/tickets/{ticket}/assign', [AdminTicketController::class, 'assign'])->name('tickets.assign');
        Route::patch('/tickets/{ticket}/status', [AdminTicketController::class, 'updateStatus'])->name('tickets.status');
        Route::post('/tickets/{ticket}/comments', [AdminTicketController::class, 'addComment'])->name('tickets.comments.store');
    });

    Route::middleware('permission:categories')->group(function () {
        Route::resource('categories', CategoryController::class)->except('show');
    });

    Route::middleware('permission:units')->group(function () {
        Route::resource('units', UnitController::class)->except('show');
    });

    Route::middleware('permission:users')->group(function () {
        Route::resource('users', UserController::class)->except('show');
    });

    Route::middleware('permission:roles')->group(function () {
        Route::resource('roles', RoleController::class)->except('show');
    });

    Route::middleware('permission:documents')->group(function () {
        Route::resource('documents', DocumentController::class)->except('show');
        Route::get('/documents/{document}/view', [DocumentController::class, 'view'])->name('documents.view');
        Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
        Route::post('/documents/{document}/test-reminder', [DocumentController::class, 'testReminder'])->name('documents.test-reminder');
    });

    Route::middleware('permission:document-types')->group(function () {
        Route::resource('document-types', DocumentTypeController::class)->except('show');
    });

    Route::middleware('permission:laporan')->group(function () {
        Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export', [ReportController::class, 'export'])->name('laporan.export');
    });

    Route::middleware('permission:whatsapp-settings')->group(function () {
        Route::get('/pengaturan/whatsapp', [WhatsappSettingController::class, 'edit'])->name('pengaturan.whatsapp');
        Route::put('/pengaturan/whatsapp', [WhatsappSettingController::class, 'update'])->name('pengaturan.whatsapp.update');
        Route::post('/pengaturan/whatsapp/test', [WhatsappSettingController::class, 'test'])->name('pengaturan.whatsapp.test');
    });

    Route::middleware('permission:google-drive-settings')->group(function () {
        Route::get('/pengaturan/google-drive', [GoogleDriveSettingController::class, 'edit'])->name('pengaturan.google-drive');
        Route::put('/pengaturan/google-drive', [GoogleDriveSettingController::class, 'update'])->name('pengaturan.google-drive.update');
        Route::get('/pengaturan/google-drive/connect', [GoogleDriveSettingController::class, 'connect'])->name('pengaturan.google-drive.connect');
        Route::get('/pengaturan/google-drive/callback', [GoogleDriveSettingController::class, 'callback'])->name('pengaturan.google-drive.callback');
        Route::post('/pengaturan/google-drive/disconnect', [GoogleDriveSettingController::class, 'disconnect'])->name('pengaturan.google-drive.disconnect');
        Route::post('/pengaturan/google-drive/test', [GoogleDriveSettingController::class, 'test'])->name('pengaturan.google-drive.test');
    });

    Route::middleware('permission:app-settings')->group(function () {
        Route::get('/pengaturan/tampilan', [AppSettingController::class, 'edit'])->name('pengaturan.tampilan');
        Route::put('/pengaturan/tampilan', [AppSettingController::class, 'update'])->name('pengaturan.tampilan.update');
        Route::post('/pengaturan/tampilan/reset', [AppSettingController::class, 'reset'])->name('pengaturan.tampilan.reset');
    });

    // calender pages
    Route::get('/calendar', function () {
        $documentEvents = [];

        if (auth()->user()->can('documents')) {
            $statusColors = [
                'expired' => 'Danger',
                'critical' => 'Danger',
                'warning' => 'Warning',
                'ok' => 'Success',
                'none' => 'Primary',
            ];

            $documentEvents = Document::query()
                ->whereNotNull('expiry_date')
                ->where('is_active', true)
                ->get()
                ->map(fn (Document $document) => [
                    'id' => 'document-reminder-'.$document->id,
                    'title' => 'Jatuh Tempo: '.$document->title,
                    'start' => $document->expiry_date->toDateString(),
                    'allDay' => true,
                    'url' => route('documents.edit', $document),
                    'extendedProps' => ['calendar' => $statusColors[$document->expiryStatus()] ?? 'Primary'],
                ])
                ->values()
                ->all();
        }

        return view('pages.calender', ['title' => 'Calendar', 'documentEvents' => $documentEvents]);
    })->name('calendar');

    // profile pages
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // form pages
    Route::get('/form-elements', function () {
        return view('pages.form.form-elements', ['title' => 'Form Elements']);
    })->name('form-elements');

    // tables pages
    Route::get('/basic-tables', function () {
        return view('pages.tables.basic-tables', ['title' => 'Basic Tables']);
    })->name('basic-tables');

    // pages
    Route::get('/blank', function () {
        return view('pages.blank', ['title' => 'Blank']);
    })->name('blank');

    // chart pages
    Route::get('/line-chart', function () {
        return view('pages.chart.line-chart', ['title' => 'Line Chart']);
    })->name('line-chart');

    Route::get('/bar-chart', function () {
        return view('pages.chart.bar-chart', ['title' => 'Bar Chart']);
    })->name('bar-chart');

    // ui elements pages
    Route::get('/alerts', function () {
        return view('pages.ui-elements.alerts', ['title' => 'Alerts']);
    })->name('alerts');

    Route::get('/avatars', function () {
        return view('pages.ui-elements.avatars', ['title' => 'Avatars']);
    })->name('avatars');

    Route::get('/badge', function () {
        return view('pages.ui-elements.badges', ['title' => 'Badges']);
    })->name('badges');

    Route::get('/buttons', function () {
        return view('pages.ui-elements.buttons', ['title' => 'Buttons']);
    })->name('buttons');

    Route::get('/image', function () {
        return view('pages.ui-elements.images', ['title' => 'Images']);
    })->name('images');

    Route::get('/videos', function () {
        return view('pages.ui-elements.videos', ['title' => 'Videos']);
    })->name('videos');
});

// error pages
Route::get('/error-404', function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
})->name('error-404');
