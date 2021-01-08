<?php

use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportRequestController;
use App\Http\Controllers\ReportsArchiveController;
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

Route::get('/', function () {
    return redirect()->intended(route('cabinet.index'));
});

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
        return redirect()->intended(route('cabinet.index'));
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
        Route::post('/send', [ChatController::class, 'createMessage'])->name('chat.send');
        Route::get('/{chat}/poll', [ChatController::class, 'pollMessages'])->name('chat.poll');
    });

    Route::prefix('/cabinet')->name('cabinet.')->group(function () {
        Route::get('/', [CabinetController::class, 'index'])->name('index');

        Route::resource('/report-requests', ReportRequestController::class);
        Route::resource('/reports', ReportController::class)->only(['show', 'edit', 'update']);

        Route::get('/report-requests/{reportRequest}/response', [ReportController::class, 'create'])
            ->name('report-requests.response');

        Route::post('/report-requests/{reportRequest}/response', [ReportController::class, 'store'])
            ->name('reports.store');

        Route::put('/reports/{report}/status', [ReportController::class, 'changeStatus'])
            ->name('reports.change-status');
        Route::put('/report-requests/{reportRequest}/status', [ReportRequestController::class, 'changeStatus'])
            ->name('report-requests.change-status');
    });

    Route::prefix('/archive')->name('archive.')->group(function () {
        Route::get('/', [ReportsArchiveController::class, 'index'])->name('index');
    });

    Route::get('/images/icons/{ext}.png', function (string $ext) {
        return redirect(asset('/images/icons/undefined.png'));
    });
});
