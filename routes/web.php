<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FolderController;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard',     [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/upload', [DashboardController::class, 'store'])->name('dashboard.upload');

    // Placeholder aman (hanya kalau belum ada controllernya)
     if (!Route::has('files.approvals.index')) {
     Route::get('/files/approvals', fn() => view('welcome'))->name('files.approvals.index');
     }
     if (!Route::has('users.index')) {
     Route::get('/users', fn() => view('welcome'))->name('users.index');
     }
     if (!Route::has('audit-logs.index')) {
     Route::get('/audit-logs', fn() => view('welcome'))->name('audit-logs.index');
     }
     if (!Route::has('roles.index')) {
     Route::get('/roles', fn() => view('welcome'))->name('roles.index');
     }
     if (!Route::has('customers.index')) {
     Route::get('/customers', fn() => view('welcome'))->name('customers.index');
     }
     if (!Route::has('transactions.index')) {
     Route::get('/transactions', fn() => view('welcome'))->name('transactions.index');
     }
     if (!Route::has('kpi.index')) {
     Route::get('/kpi', fn() => view('welcome'))->name('kpi.index');
     }


    // File CRUD + view/download
    Route::resource('files', FileController::class);
    Route::get('files/{file}/view',     [FileController::class, 'view'])->name('files.view');
    Route::get('files/{file}/download', [FileController::class, 'download'])->name('files.download');
    Route::get('/my-files', [FileController::class, 'myFiles'])->name('files.myfiles');
    Route::post('/files/{file}/favorite', [FileController::class, 'toggleFavorite'])
     ->name('files.favorite');

     Route::get('/favorites', [FileController::class, 'favorites'])
     ->name('files.favorites');


    // Profile
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Kategori
    Route::get('/category/{id}', [DashboardController::class, 'showCategory'])->name('category.show');
    Route::put('/category/{id}', [DashboardController::class, 'updateCategory'])->name('category.update');

    // **SHOW FOLDER** dalam kategori
    Route::get(
      '/category/{category}/folders/{folder}',
      [DashboardController::class, 'showFolder']
    )->name('category.folder');

    // Folder CRUD (store/update/destroy) — hanya 3 route ini:
    Route::post('/categories/{category}/folders', [FolderController::class, 'store'])
         ->name('folders.store');
    Route::put('/folders/{id}',    [FolderController::class, 'update'])
         ->name('folders.update');
    Route::delete('/folders/{id}', [FolderController::class, 'destroy'])
         ->name('folders.destroy');
});

require __DIR__.'/auth.php';
