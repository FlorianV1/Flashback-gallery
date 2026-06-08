<?php

use App\Http\Controllers\AlbumAccessController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\QrCodeController;
use App\Livewire\AlbumDetail;
use App\Livewire\Gallery;
use App\Livewire\UnlockAlbum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', Gallery::class)->name('gallery');
Route::get('/albums/{album}', AlbumDetail::class)->name('albums.show');
Route::get('/albums/{album}/unlock', UnlockAlbum::class)->name('albums.unlock');
Route::get('/albums/{album}/photos/{photo}/download', [PhotoController::class, 'download'])
    ->name('photos.download');

Route::get('/join/{token}', AlbumAccessController::class)->name('albums.join');

// Redirect legacy login/register to Filament
Route::get('/login', fn () => redirect('/admin/login'))->name('login');
Route::get('/register', fn () => redirect('/admin/register'))->name('register');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout')->middleware('auth');

Route::get('/admin/qr/{album}', QrCodeController::class)
    ->name('albums.qr')
    ->middleware('auth');
