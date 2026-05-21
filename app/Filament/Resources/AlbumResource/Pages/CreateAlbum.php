<?php

namespace App\Filament\Resources\AlbumResource\Pages;

use App\Filament\Resources\AlbumResource;
use App\Models\Photo;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateAlbum extends CreateRecord
{
    protected static string $resource = AlbumResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $state = $this->form->getState();
        $photos = array_values(array_filter((array) ($state['photos_upload'] ?? [])));
        $names = (array) ($state['photos_upload_names'] ?? []);

        if (empty($photos)) {
            return;
        }

        $album = $this->record;
        $maxOrder = 0;

        foreach ($photos as $tempPath) {
            $basename = basename($tempPath);
            $newPath = 'albums/' . $album->id . '/' . $basename;
            Storage::disk('public')->move($tempPath, $newPath);

            Photo::create([
                'album_id' => $album->id,
                'filename' => $newPath,
                'original_filename' => $names[$tempPath] ?? $basename,
                'sort_order' => ++$maxOrder,
            ]);
        }
    }
}
