<?php

namespace App\Livewire;

use App\Models\Album;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AlbumDetail extends Component
{
    public Album $album;

    public function mount(Album $album): void
    {
        if (! $album->isUnlocked()) {
            $this->redirectRoute('albums.unlock', $album);
        }
    }

    public function render()
    {
        $photos = $this->album->photos()->orderBy('sort_order')->get();

        $downloadUrls = $photos->map(
            fn ($photo) => route('photos.download', ['album' => $this->album->id, 'photo' => $photo->id])
        )->values()->toArray();

        return view('livewire.album-detail', [
            'photos' => $photos,
            'downloadUrls' => $downloadUrls,
        ])->title($this->album->title . ' — Flashback Gallery');
    }
}
