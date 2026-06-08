<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlbumResource\Pages;
use App\Filament\Resources\AlbumResource\RelationManagers\PhotosRelationManager;
use App\Models\Album;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class AlbumResource extends Resource
{
    protected static ?string $model = Album::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Albums';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->check() && ! auth()->user()->isAdmin()) {
            $query->whereHas('users', fn ($q) => $q->where('users.id', auth()->id()));
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        $isAdmin = auth()->user()?->isAdmin() ?? false;

        return $schema
            ->components([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->disabled(! $isAdmin)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->disabled(! $isAdmin)
                    ->columnSpanFull(),

                Forms\Components\DatePicker::make('date_of_outing')
                    ->required()
                    ->native(false)
                    ->default(today())
                    ->displayFormat('M j, Y')
                    ->disabled(! $isAdmin),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->disabled(! $isAdmin),

                Forms\Components\Toggle::make('is_public')
                    ->label('Public Album (no access code required)')
                    ->live()
                    ->disabled(! $isAdmin)
                    ->afterStateUpdated(fn (Set $set, bool $state) => $state ? $set('access_code', null) : null)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('access_code')
                    ->label('Access Code')
                    ->maxLength(20)
                    ->placeholder('e.g. ocean74')
                    ->helperText('Lowercase letters and numbers only, no spaces. Example: "hawk21", "cedar09"')
                    ->disabled(! $isAdmin)
                    ->hidden(fn (Get $get) => (bool) $get('is_public'))
                    ->dehydrateStateUsing(fn (?string $state) => $state ? strtolower(preg_replace('/\s+/', '', $state)) : null)
                    ->suffixActions([
                        Action::make('generateCode')
                            ->label('Generate')
                            ->icon('heroicon-o-arrow-path')
                            ->action(fn (Set $set) => $set('access_code', self::generateAccessCode()))
                            ->tooltip('Generate a memorable access code')
                            ->hidden(! $isAdmin),
                    ]),

                Section::make('Contributors')
                    ->schema([
                        Forms\Components\Select::make('users')
                            ->label('Assigned Contributors')
                            ->multiple()
                            ->relationship('users', 'name', fn ($query) => $query->where('role', 'contributor'))
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),
                    ])
                    ->visible($isAdmin)
                    ->columnSpanFull(),

                Section::make('Photos')
                    ->schema([
                        Forms\Components\FileUpload::make('photos_upload')
                            ->label('Upload Photos')
                            ->multiple()
                            ->maxFiles(60)
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                            ->disk('public')
                            ->directory(fn ($record) => $record ? 'albums/' . $record->id : 'albums/upload-tmp')
                            ->storeFileNamesIn('photos_upload_names')
                            ->image()
                            ->imagePreviewHeight('110')
                            ->panelLayout('grid'),
                        Forms\Components\Hidden::make('photos_upload_names'),
                    ])
                    ->columnSpanFull(),

                Section::make('Share Link')
                    ->schema([
                        Forms\Components\Placeholder::make('share_link_info')
                            ->label('')
                            ->content(function ($record) {
                                if (! $record) {
                                    return new HtmlString('<p class="text-sm" style="color:#8B7355;">Save the album first to get its share link.</p>');
                                }

                                $url = $record->shareUrl();

                                $options = new QROptions([
                                    'outputType' => QRCode::OUTPUT_MARKUP_SVG,
                                    'svgAddXmlHeader' => false,
                                    'drawLightModules' => false,
                                    'scale' => 5,
                                ]);
                                $qrDataUri = (new QRCode($options))->render($url);

                                return new HtmlString('
                                    <div style="display:grid; grid-template-columns:1fr auto; gap:2rem; align-items:start;">
                                        <div style="min-width:0;">
                                            <p style="font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.1em; color:#A89880; margin:0 0 0.5rem;">Access Link</p>
                                            <p style="font-size:0.75rem; color:#8B7355; margin:0 0 0.75rem;">Anyone with this link can open the album without needing the access code.</p>
                                            <div style="display:flex; gap:0.5rem; align-items:center;">
                                                <code style="flex:1; min-width:0; font-size:0.7rem; padding:0.5rem 0.75rem; border-radius:4px; background:#F0EBE2; color:#2C1810; font-family:monospace; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:block;">' . e($url) . '</code>
                                                <button type="button"
                                                        onclick="(function(btn,text){if(navigator.clipboard){navigator.clipboard.writeText(text).then(function(){btn.textContent=\'Copied!\';setTimeout(function(){btn.textContent=\'Copy\'},1500)});}else{var ta=document.createElement(\'textarea\');ta.value=text;ta.style.position=\'fixed\';ta.style.opacity=\'0\';document.body.appendChild(ta);ta.select();document.execCommand(\'copy\');document.body.removeChild(ta);btn.textContent=\'Copied!\';setTimeout(function(){btn.textContent=\'Copy\'},1500);}})(this,\'' . e($url) . '\')"
                                                        style="flex-shrink:0; padding:0.5rem 0.875rem; font-size:0.75rem; border-radius:4px; font-weight:600; background:#2C1810; color:#FAF7F2; border:none; cursor:pointer; transition:opacity 0.15s;"
                                                        onmouseover="this.style.opacity=\'0.85\'" onmouseout="this.style.opacity=\'1\'">
                                                    Copy
                                                </button>
                                            </div>
                                        </div>
                                        <div style="flex-shrink:0; text-align:center;">
                                            <p style="font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.1em; color:#A89880; margin:0 0 0.5rem;">QR Code</p>
                                            <img src="' . $qrDataUri . '" alt="QR Code" style="width:140px; height:140px; border-radius:4px; border:1px solid #DDD5C5; padding:8px; background:#fff; display:block;">
                                            <button type="button"
                                                    wire:click="regenerateShareLink"
                                                    wire:confirm="Regenerate the QR code and share link? The old link will stop working immediately."
                                                    style="margin-top:0.5rem; width:100%; padding:0.375rem 0.5rem; font-size:0.7rem; border-radius:4px; font-weight:500; background:transparent; color:#8B7355; border:1px solid #DDD5C5; cursor:pointer; transition:all 0.15s;"
                                                    onmouseover="this.style.background=\'#F0EBE2\'" onmouseout="this.style.background=\'transparent\'">
                                                ↺ Regenerate
                                            </button>
                                        </div>
                                    </div>
                                ');
                            })
                            ->columnSpanFull(),
                    ])
                    ->visible($isAdmin)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width('50px'),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('date_of_outing')
                    ->date('M j, Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-open')
                    ->falseIcon('heroicon-o-lock-closed')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->width('50px'),

                Tables\Columns\TextColumn::make('photos_count')
                    ->label('Media')
                    ->counts('photos')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('access_code')
                    ->label('Code')
                    ->copyable()
                    ->copyMessage('Code copied!')
                    ->placeholder('—')
                    ->fontFamily('mono')
                    ->weight('bold'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Visibility')
                    ->trueLabel('Public')
                    ->falseLabel('Private'),
            ])
            ->recordActions([
                EditAction::make(),

                DeleteAction::make()
                    ->visible(fn () => auth()->user()?->isAdmin() ?? false)
                    ->before(function (Album $record) {
                        foreach ($record->photos as $photo) {
                            Storage::disk('public')->delete($photo->filename);
                        }
                        Storage::disk('public')->deleteDirectory('albums/' . $record->id);
                    }),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }

    public static function getRelations(): array
    {
        return [
            PhotosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlbums::route('/'),
            'create' => Pages\CreateAlbum::route('/create'),
            'edit' => Pages\EditAlbum::route('/{record}/edit'),
        ];
    }

    public static function generateAccessCode(): string
    {
        $words = [
            'blue', 'red', 'jade', 'rose', 'sage', 'teal', 'ruby', 'gold', 'aqua', 'lime',
            'cloud', 'wave', 'moon', 'star', 'pine', 'rain', 'snow', 'wind', 'lake', 'fire',
            'sand', 'mist', 'dawn', 'dusk', 'oak', 'elm', 'bear', 'fox', 'wolf', 'hawk',
            'deer', 'swan', 'crow', 'owl', 'stone', 'glass', 'wood', 'iron', 'silk', 'amber',
            'coral', 'cedar', 'maple', 'river', 'ocean', 'sunny', 'storm', 'frost', 'bloom',
        ];

        return $words[array_rand($words)] . rand(10, 99);
    }
}
