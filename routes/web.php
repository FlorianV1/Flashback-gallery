<?php

use App\Http\Controllers\PhotoController;
use App\Livewire\AlbumDetail;
use App\Livewire\Gallery;
use App\Livewire\UnlockAlbum;
use Illuminate\Support\Facades\Route;

Route::get('/', Gallery::class)->name('gallery');
Route::get('/albums/{album}', AlbumDetail::class)->name('albums.show');
Route::get('/albums/{album}/unlock', UnlockAlbum::class)->name('albums.unlock');
Route::get('/albums/{album}/photos/{photo}/download', [PhotoController::class, 'download'])
    ->name('photos.download');
