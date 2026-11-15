<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class PhotoController extends Controller
{
    public function download(Album $album, Photo $photo)
    {
        abort_if(! $album->isUnlocked(), 403, 'This album is locked.');
        abort_if($photo->album_id !== $album->id, 404);

        $path = Storage::disk('public')->path($photo->filename);
        abort_if(! file_exists($path), 404, 'Photo file not found.');

        // Let Symfony derive Content-Type from the actual file — hardcoding
        // image/png made iOS Safari save JPEGs it then refused to open.
        return response()->download($path, $photo->original_filename, [
            'Cache-Control' => 'no-store',
        ]);
    }

    public function downloadAlbum(Album $album)
    {
        abort_if(! $album->isUnlocked(), 403, 'This album is locked.');

        $photos = $album->photos()->orderBy('sort_order')->get();
        abort_if($photos->isEmpty(), 404, 'This album has no photos.');

        @set_time_limit(0);

        $zipPath = tempnam(sys_get_temp_dir(), 'album-');
        $zip = new ZipArchive;

        abort_if(
            $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true,
            500,
            'Could not build the download.'
        );

        $used = [];

        foreach ($photos as $photo) {
            $path = Storage::disk('public')->path($photo->filename);

            if (! file_exists($path)) {
                continue;
            }

            $name = $this->uniqueEntryName($photo->original_filename ?: basename($photo->filename), $used);

            $zip->addFile($path, $name);
            // Photos and videos are already compressed; storing is much faster.
            $zip->setCompressionName($name, ZipArchive::CM_STORE);
        }

        $empty = $zip->numFiles === 0;
        $zip->close();

        if ($empty) {
            @unlink($zipPath);
            abort(404, 'No photo files were found for this album.');
        }

        $zipName = (Str::slug($album->title) ?: 'album').'.zip';

        return response()->download($zipPath, $zipName, [
            'Content-Type' => 'application/zip',
            'Cache-Control' => 'no-store',
        ])->deleteFileAfterSend();
    }

    private function uniqueEntryName(string $filename, array &$used): string
    {
        $base = pathinfo($filename, PATHINFO_FILENAME);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $ext = $ext !== '' ? '.'.$ext : '';

        $name = $base.$ext;
        $i = 1;

        while (isset($used[strtolower($name)])) {
            $name = $base.'-'.(++$i).$ext;
        }

        $used[strtolower($name)] = true;

        return $name;
    }
}
