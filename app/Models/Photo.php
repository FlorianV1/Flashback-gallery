<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    protected $fillable = [
        'album_id',
        'filename',
        'original_filename',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function getUrl(): string
    {
        return Storage::disk('public')->url($this->filename);
    }

    public function getPath(): string
    {
        return Storage::disk('public')->path($this->filename);
    }
}
