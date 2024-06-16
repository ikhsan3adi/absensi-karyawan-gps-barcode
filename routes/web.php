<?php

use App\Http\Controllers\Admin\BarcodeController;
use App\Http\Controllers\Admin\MasterDataController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');
    return redirect('/login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/', fn () => redirect('/home'));
    Route::get('/home', HomeController::class)->name('home');

    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/', fn () => redirect('/admin/dashboard'));
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        // Barcode
        Route::resource('/barcodes', BarcodeController::class)
            ->only(['index', 'show', 'create', 'store', 'edit', 'update'])
            ->names([
                'index' => 'admin.barcodes',
                'show' => 'admin.barcodes.show',
                'create' => 'admin.barcodes.create',
                'store' => 'admin.barcodes.store',
                'edit' => 'admin.barcodes.edit',
                'update' => 'admin.barcodes.update',
            ]);
        Route::get('/barcodes/download/all', [BarcodeController::class, 'downloadAll'])
            ->name('admin.barcodes.downloadall');
        Route::get('/barcodes/{id}/download', [BarcodeController::class, 'download'])
            ->name('admin.barcodes.download');

        // User/Employee/Karyawan
        Route::resource('/users', UserController::class)->names([
            'index' => 'admin.users',
            'create' => 'admin.users.create',
            'store' => 'admin.users.store',
            'edit' => 'admin.users.edit',
            'update' => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ]);

        // Master Data
        Route::get('/masterdata', MasterDataController::class)
            ->name('admin.masters');

        // // Presence/Absensi
        // Route::get('/presence', [App\Http\Controllers\Admin\PresenceController::class, 'index'])
        //     ->name('admin.presence');

        // // Reports/Laporan
        // Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])
        //     ->name('admin.reports');
    });
});
