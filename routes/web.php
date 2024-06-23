<?php

use App\Http\Controllers\Admin\BarcodeController;
use App\Http\Controllers\Admin\MasterDataController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserAttendanceController;
use Illuminate\Support\Facades\Auth;
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
    Route::get('/', fn () => Auth::user()->group == 'admin' ? redirect('/admin') : redirect('/home'));

    // USER AREA
    Route::middleware('user')->group(function () {
        Route::get('/home', HomeController::class)->name('home');

        Route::get('/apply-leave', [UserAttendanceController::class, 'applyLeave'])
            ->name('apply-leave');
        Route::post('/apply-leave', [UserAttendanceController::class, 'storeLeaveRequest'])
            ->name('store-leave-request');

        Route::get('/attendance-history', [UserAttendanceController::class, 'history'])
            ->name('attendance-history');
    });

    // ADMIN AREA
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
        Route::resource('/employee', EmployeeController::class)
            ->only(['index'])
            ->names(['index' => 'admin.employees']);

        // Master Data
        Route::get('/masterdata', MasterDataController::class)
            ->name('admin.masters');

        // Presence/Absensi
        Route::get('/attendances', [AttendanceController::class, 'index'])
            ->name('admin.attendances');

        // Presence/Absensi
        Route::get('/attendances/report', [AttendanceController::class, 'report'])
            ->name('admin.attendances.report');
    });
});
