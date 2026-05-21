<?php

namespace App\Filament\Resources\AlbumResource\RelationManagers;

use App\Models\Photo;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class PhotosRelationManager extends RelationManager
{
    protected static string $relationship = 'photos';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('filename')
                    ->label('')
                    ->disk('public')
                    ->square()
                    ->size(80),

                Tables\Columns\TextColumn::make('original_filename')
                    ->label('Filename')
                    ->searchable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width('50px'),

                Tables\Columns\IconColumn::make('is_cover')
                    ->label('Cover')
                    ->getStateUsing(fn (Photo $record): bool => $this->ownerRecord->cover_photo_id === $record->id)
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                Action::make('upload')
                    ->label('Upload Photos')
                    ->icon('heroicon-o-cloud-arrow-up')
                    ->form(function (): array {
                        $albumId = $this->ownerRecord->id;

                        return [
                            Forms\Components\FileUpload::make('photos')
                                ->label('Photos')
                                ->multiple()
                                ->maxFiles(60)
                                ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                                ->disk('public')
                                ->directory('albums/' . $albumId)
                                ->storeFileNamesIn('original_filenames')
                                ->image()
                                ->imagePreviewHeight('110')
                                ->panelLayout('grid')
                                ->reorderable(),
                            Forms\Components\Hidden::make('original_filenames'),
                        ];
                    })
                    ->action(function (array $data): void {
                        $photos = $data['photos'] ?? [];
                        $originalFilenames = $data['original_filenames'] ?? [];
                        $maxOrder = $this->ownerRecord->photos()->max('sort_order') ?? 0;

                        foreach ($photos as $storedPath) {
                            $maxOrder++;
                            Photo::create([
                                'album_id' => $this->ownerRecord->id,
                                'filename' => $storedPath,
                                'original_filename' => $originalFilenames[$storedPath] ?? basename($storedPath),
                                'sort_order' => $maxOrder,
                            ]);
                        }

                        Notification::make()
                            ->title(count($photos) . ' photo(s) uploaded.')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                Action::make('set_cover')
                    ->label('Set Cover')
                    ->icon('heroicon-o-star')
                    ->color(fn (Photo $record): string => $this->ownerRecord->cover_photo_id === $record->id ? 'warning' : 'gray')
                    ->action(function (Photo $record): void {
                        $this->ownerRecord->update(['cover_photo_id' => $record->id]);
                        Notification::make()->title('Cover photo updated.')->success()->send();
                    }),

                DeleteAction::make()
                    ->before(function (Photo $record): void {
                        if ($this->ownerRecord->cover_photo_id === $record->id) {
                            $this->ownerRecord->update(['cover_photo_id' => null]);
                        }
                        Storage::disk('public')->delete($record->filename);
                    }),
            ]);
    }
}
