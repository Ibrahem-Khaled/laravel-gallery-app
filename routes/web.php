<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GalleryImageController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::post('images/upload', [GalleryImageController::class, 'store'])->name('images.store');
    Route::delete('images/{image}', [GalleryImageController::class, 'destroy'])->name('images.destroy');
    Route::get('images/{image}/logs', [GalleryImageController::class, 'logs'])->name('images.logs');
    Route::post('categories/{category}/share', [CategoryController::class, 'share'])->name('categories.share');
    Route::get('analytics', [GalleryImageController::class, 'allLogs'])->name('analytics.index');
});

Route::get('s/{token}', [GalleryImageController::class, 'show'])->name('images.show');
Route::get('c/{token}', [CategoryController::class, 'showPublic'])->name('categories.public');
Route::post('api/visitor-data', [GalleryImageController::class, 'storeVisitorData'])->name('visitor.data.store')->middleware('web');

Route::get('/dashboard', [GalleryImageController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
