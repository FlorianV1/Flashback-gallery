<?php

namespace App\Filament\Resources\AlbumResource\Pages;

use App\Filament\Resources\AlbumResource;
use App\Models\Photo;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditAlbum extends EditRecord
{
    protected static string $resource = AlbumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_album')
                ->label('View Album')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn () => route('albums.show', $this->record))
                ->openUrlInNewTab()
                ->color('gray'),

            Actions\DeleteAction::make(),
        ];
    }

    public function regenerateShareLink(): void
    {
        $this->record->update(['share_token' => Str::uuid()->toString()]);
        $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record]));
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
