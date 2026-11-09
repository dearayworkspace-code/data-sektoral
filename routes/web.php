<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DinasAuthController;
use App\Http\Controllers\DinasPortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Portal Mapping Data Dinas & Google Spreadsheet (BPS)
|--------------------------------------------------------------------------
*/

// ==========================================
// RUTE ROLE DINAS
// ==========================================
Route::get('/', [DinasAuthController::class, 'showLogin'])->name('dinas.login');
Route::post('/login', [DinasAuthController::class, 'login'])->name('dinas.login.submit');
Route::post('/logout', [DinasAuthController::class, 'logout'])->name('dinas.logout');

Route::middleware('dinas.auth')->group(function () {
    // Form Onboarding Pertama Kali Login (Pengisian PIC & Opsi Ganti PIN)
    Route::get('/first-login', [DinasAuthController::class, 'showFirstLogin'])->name('dinas.first_login');
    Route::post('/first-login', [DinasAuthController::class, 'saveFirstLogin'])->name('dinas.first_login.save');

    // Halaman Perantara (Landing Page Dinas)
    Route::get('/portal', [DinasPortalController::class, 'index'])->name('dinas.portal');

    // Halaman Tampilan Embed Google Spreadsheet (Tanpa Tombol Buka Tab Baru)
    Route::get('/portal/sheet', [DinasPortalController::class, 'openSpreadsheet'])->name('dinas.embed');

    // Update Mandiri Data PIC & Ganti PIN Dinas
    Route::post('/portal/profile', [DinasPortalController::class, 'updateProfile'])->name('dinas.profile.update');

    // Interaktif Tabel Dinas (Checklist Selesai & Tambah Catatan Mandiri)
    Route::post('/portal/tabel/{id}/toggle-status', [DinasPortalController::class, 'toggleTabelStatus'])->name('dinas.tabel.toggle_status');
    Route::post('/portal/tabel/{id}/catatan', [DinasPortalController::class, 'updateCatatanDinas'])->name('dinas.tabel.catatan');
});

// ==========================================
// RUTE ROLE ADMIN BPS
// ==========================================
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::middleware('admin.auth')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Manajemen Dinas (Manual CRUD)
        Route::post('/dinas', [AdminDashboardController::class, 'store'])->name('admin.dinas.store');
        Route::put('/dinas/{id}', [AdminDashboardController::class, 'update'])->name('admin.dinas.update');
        Route::put('/dinas/{id}/reset-pin', [AdminDashboardController::class, 'resetPin'])->name('admin.dinas.reset_pin');
        Route::delete('/dinas/{id}', [AdminDashboardController::class, 'destroy'])->name('admin.dinas.destroy');

        // Manajemen Verifikasi Tabel BPS
        Route::put('/tabel/{id}/toggle-data-masuk', [AdminDashboardController::class, 'toggleDataMasuk'])->name('admin.tabel.toggle_data_masuk');

        // Import & Export
        Route::get('/template-import', [AdminDashboardController::class, 'downloadTemplate'])->name('admin.dinas.template');
        Route::post('/import', [AdminDashboardController::class, 'import'])->name('admin.dinas.import');
        Route::get('/export', [AdminDashboardController::class, 'export'])->name('admin.dinas.export');
    });
});

