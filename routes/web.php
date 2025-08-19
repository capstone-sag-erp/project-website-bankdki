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

    // File CRUD + view/download
    Route::resource('files', FileController::class);
    Route::get('files/{file}/view',     [FileController::class, 'view'])->name('files.view');
    Route::get('files/{file}/download', [FileController::class, 'download'])->name('files.download');

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
