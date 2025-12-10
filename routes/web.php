<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FolderController;

Route::get('/', fn() => redirect()->route('dashboard'));

// ====================
// ROUTE YANG BUTUH LOGIN
// ====================
Route::middleware(['auth'])->group(function () {

    // ==================== Dashboard ====================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/upload', [DashboardController::class, 'store'])->name('dashboard.upload');

    // ==================== File CRUD + View/Download ====================
    Route::resource('files', FileController::class);
    Route::get('files/{file}/view', [FileController::class, 'view'])->name('files.view');
    Route::get('files/{file}/download', [FileController::class, 'download'])->name('files.download');
    Route::get('/my-files', [FileController::class, 'myFiles'])->name('files.myfiles');
    Route::post('/files/{file}/favorite', [FileController::class, 'toggleFavorite'])->name('files.favorite');
    Route::get('/favorites', [FileController::class, 'favorites'])->name('files.favorites');

    // ==================== Profile ====================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ==================== Kategori & Folder ====================
    Route::get('/category/{id}', [DashboardController::class, 'showCategory'])->name('category.show');
    Route::put('/category/{id}', [DashboardController::class, 'updateCategory'])->name('category.update');

    Route::get(
        '/category/{category}/folders/{folder}',
        [DashboardController::class, 'showFolder']
    )->name('category.folder');

    Route::post('/categories/{category}/folders', [FolderController::class, 'store'])->name('folders.store');
    Route::put('/folders/{id}', [FolderController::class, 'update'])->name('folders.update');
    Route::delete('/folders/{id}', [FolderController::class, 'destroy'])->name('folders.destroy');


    // ==================== ERP - AKSES MANAGER & STAFF (Input Data) ====================
    Route::middleware(['role:manager,staff'])->group(function () {
        // ERP - Nasabah (manager & staff bisa input/edit)
        Route::get('/erp/nasabah/create', [App\Http\Controllers\ErpController::class, 'createNasabah'])->name('erp.nasabah.create');
        Route::post('/erp/nasabah', [App\Http\Controllers\ErpController::class, 'storeNasabah'])->name('erp.nasabah.store');
        Route::get('/erp/nasabah/{nasabah}/edit', [App\Http\Controllers\ErpController::class, 'editNasabah'])->name('erp.nasabah.edit');
        Route::put('/erp/nasabah/{nasabah}', [App\Http\Controllers\ErpController::class, 'updateNasabah'])->name('erp.nasabah.update');

        // ERP - Transaksi (manager & staff bisa input/edit)
        Route::get('/erp/transaksi/create', [App\Http\Controllers\ErpController::class, 'createTransaksi'])->name('erp.transaksi.create');
        Route::post('/erp/transaksi', [App\Http\Controllers\ErpController::class, 'storeTransaksi'])->name('erp.transaksi.store');
        Route::get('/erp/transaksi/{transaction}/edit', [App\Http\Controllers\ErpController::class, 'editTransaksi'])->name('erp.transaksi.edit');
        Route::put('/erp/transaksi/{transaction}', [App\Http\Controllers\ErpController::class, 'updateTransaksi'])->name('erp.transaksi.update');
    });

    // ==================== AKSES KHUSUS MANAGER ====================
    Route::middleware(['role:manager'])->group(function () {
        // Workflow approvals (hanya manager)
        Route::get('/workflow/approvals', [App\Http\Controllers\WorkflowController::class, 'index'])
            ->middleware('perm:workflow.view')
            ->name('workflow.index');

        Route::post('/workflow/approve/{file}', [App\Http\Controllers\WorkflowController::class, 'approve'])
            ->middleware('perm:workflow.approve')
            ->name('workflow.approve');

        Route::post('/workflow/reject/{file}', [App\Http\Controllers\WorkflowController::class, 'reject'])
            ->middleware('perm:workflow.approve')
            ->name('workflow.reject');

        // KPI Dashboard (hanya manager)
        Route::get('/kpi', [App\Http\Controllers\KpiController::class, 'index'])
            ->middleware('perm:kpi.view')
            ->name('kpi.index');

        Route::post('/kpi/update-monthly', [App\Http\Controllers\KpiController::class, 'updateKpiMonthly'])
            ->name('kpi.update-monthly');

        // ERP - Delete (hanya manager)
        Route::delete('/erp/nasabah/{nasabah}', [App\Http\Controllers\ErpController::class, 'destroyNasabah'])->name('erp.nasabah.destroy');
        Route::delete('/erp/transaksi/{transaction}', [App\Http\Controllers\ErpController::class, 'destroyTransaksi'])->name('erp.transaksi.destroy');

        // Halaman yang hanya boleh manager
        Route::get('/users', fn() => view('welcome'))->name('users.index');
        Route::get('/audit-logs', fn() => view('welcome'))->name('audit-logs.index');
        Route::get('/roles', fn() => view('welcome'))->name('roles.index');
    });

    // ==================== AKSES KHUSUS STAFF ====================
    Route::middleware(['role:staff'])->group(function () {
        // Staff bisa lihat status submission mereka
        Route::get('/workflow/my-submissions', [App\Http\Controllers\WorkflowController::class, 'mySubmissions'])
            ->name('workflow.my-submissions');

        Route::post('/workflow/submit/{file}', [App\Http\Controllers\WorkflowController::class, 'submit'])
            ->name('workflow.submit');
    });

    // ==================== ERP - AKSES MANAGER & STAFF ====================
    // Manager: Full CRUD, Staff: Read Only
    Route::get('/erp/nasabah', [App\Http\Controllers\ErpController::class, 'indexNasabah'])->name('erp.nasabah.index');
    Route::get('/erp/nasabah/{nasabah}', [App\Http\Controllers\ErpController::class, 'showNasabah'])->name('erp.nasabah.show');
    Route::get('/erp/transaksi', [App\Http\Controllers\ErpController::class, 'indexTransaksi'])->name('erp.transaksi.index');
    Route::get('/erp/rekap', [App\Http\Controllers\ErpController::class, 'rekap'])->name('erp.rekap');
});

require __DIR__ . '/auth.php';
