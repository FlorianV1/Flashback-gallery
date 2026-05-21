<?php

namespace App\Filament\Resources\AlbumResource\Pages;

use App\Filament\Resources\AlbumResource;
use App\Models\Photo;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAlbum extends EditRecord
{
    protected static string $resource = AlbumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        $state = $this->form->getState();
        $photos = array_values(array_filter((array) ($state['photos_upload'] ?? [])));
        $names = (array) ($state['photos_upload_names'] ?? []);

        if (empty($photos)) {
            return;
        }

        $album = $this->record;
        $maxOrder = $album->photos()->max('sort_order') ?? 0;

        foreach ($photos as $storedPath) {
            Photo::create([
                'album_id' => $album->id,
                'filename' => $storedPath,
                'original_filename' => $names[$storedPath] ?? basename($storedPath),
                'sort_order' => ++$maxOrder,
            ]);
        }
    }
}
