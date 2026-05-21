<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Album extends Model
{
    protected $fillable = [
        'title',
        'description',
        'date_of_outing',
        'is_public',
        'access_code',
        'sort_order',
        'cover_photo_id',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'date_of_outing' => 'date',
        'sort_order' => 'integer',
        'cover_photo_id' => 'integer',
    ];

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class)->orderBy('sort_order');
    }

    public function coverPhoto(): BelongsTo
    {
        return $this->belongsTo(Photo::class, 'cover_photo_id');
    }

    public function isUnlocked(): bool
    {
        if ($this->is_public) {
            return true;
        }

        return in_array($this->id, session('unlocked_albums', []));
    }

    public function checkCode(string $code): bool
    {
        if (empty($this->access_code)) {
            return false;
        }

        return strtoupper(trim($code)) === strtoupper($this->access_code);
    }
}
