<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Laravel\Facades\Image;

class ImageProcessor
{
    public function generateVariants(string $storedPath): void
    {
        $disk = Storage::disk('public');
        $fullPath = $disk->path($storedPath);

        $dir = dirname($storedPath);
        $base = pathinfo($storedPath, PATHINFO_FILENAME);

        $disk->makeDirectory($dir . '/thumbs');
        $disk->makeDirectory($dir . '/web');

        // 600×600 square center crop for the grid
        Image::read($fullPath)
            ->orient()
            ->cover(600, 600)
            ->encode(new JpegEncoder(85))
            ->save($disk->path($dir . '/thumbs/' . $base . '.jpg'));

        // Max 1920px on longest side for the lightbox
        Image::read($fullPath)
            ->orient()
            ->scaleDown(1920, 1920)
            ->encode(new JpegEncoder(85))
            ->save($disk->path($dir . '/web/' . $base . '.jpg'));
    }

    public function deleteVariants(string $storedPath): void
    {
        $dir = dirname($storedPath);
        $base = pathinfo($storedPath, PATHINFO_FILENAME);

        Storage::disk('public')->delete([
            $dir . '/thumbs/' . $base . '.jpg',
            $dir . '/web/' . $base . '.jpg',
        ]);
    }
}
