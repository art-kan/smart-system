<?php

use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('login', function () {
    return viewMobileOrDesktop('login');
})->name('login');

Route::post('login', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => ['required', 'email', 'exists:\App\Models\User,email'],
        'password' => ['required', 'string'],
    ]);

    if (Auth::attempt($request->only(['email', 'password']))) {
        $request->session()->regenerate();
        return redirect()->intended(route('home'));
    }

    if ($request->expectsJson()) {
        return response('bad', 401);
    }

    return redirect(\route('login'));
});

Route::middleware('auth')->group(function () {
    Route::prefix('/archive')->group(function () {
        Route::get('/{filename}', [ArchiveController::class, 'getFile'])->name('archive.file');
    });

    Route::prefix('/chat')->group(function () {
        Route::post('/send', [ChatController::class, 'receiveMessage'])->name('chat.send');
        Route::get('/{chat}/poll', [ChatController::class, 'pollMessages'])->name('chat.poll');
    });

    Route::prefix('/cabinet')->group(function () {
        Route::get('/', function () {
            return redirect('/cabinet/report-requests');
        })->name('home');

        Route::prefix('/report-requests')->group(function () {
            Route::get('/create',
                [CabinetController::class, 'getReportRequestEditor'])->name('cabinet.report-request.creator');

            Route::get('/{report_request?}',
                [CabinetController::class, 'getReportRequest'])->name('cabinet.report-request');

            Route::get('/{report_request}/edit',
                [CabinetController::class, 'getReportRequestEditor'])->name('cabinet.report-request.editor');

            Route::put('/{report_request}',
                [CabinetController::class, 'updateReportRequest'])->name('cabinet.report-request.edit');

            Route::post('/',
                [CabinetController::class, 'createReportRequest'])->name('cabinet.report-request.create');
        });


        Route::prefix('/reports')->group(function () {
            Route::put('/{report}', [CabinetController::class, 'updateReport'])->name('cabinet.report.edit');
            Route::get('/response/{report_request}', [CabinetController::class, 'getReportEditor'])->name('cabinet.report.creator');
            Route::get('/{report}', [CabinetController::class, 'getReport'])->name('cabinet.report');


            Route::post('/response/{report_request}', [CabinetController::class, 'createReport'])
                ->name('cabinet.report.create');
        });
    });
});
