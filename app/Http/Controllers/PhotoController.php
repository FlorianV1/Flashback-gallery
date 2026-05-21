<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function download(Album $album, Photo $photo)
    {
        abort_if(! $album->isUnlocked(), 403, 'This album is locked.');
        abort_if($photo->album_id !== $album->id, 404);

        $path = Storage::disk('public')->path($photo->filename);
        abort_if(! file_exists($path), 404, 'Photo file not found.');

        return response()->download($path, $photo->original_filename, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store',
        ]);
    }
}
