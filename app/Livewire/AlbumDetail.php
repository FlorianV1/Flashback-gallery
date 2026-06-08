<?php

namespace App\Livewire;

use App\Models\Album;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AlbumDetail extends Component
{
    use WithPagination;

    public Album $album;

    public function mount(Album $album): void
    {
        if (! $album->isUnlocked()) {
            $this->redirectRoute('albums.unlock', $album);
        }
    }

    public function render()
    {
        $photos = $this->album->photos()->orderBy('sort_order')->paginate(50);

        $downloadUrls = $photos->getCollection()->map(
            fn ($photo) => route('photos.download', ['album' => $this->album->id, 'photo' => $photo->id])
        )->values()->toArray();

        return view('livewire.album-detail', [
            'photos' => $photos,
            'downloadUrls' => $downloadUrls,
        ])->title($this->album->title . ' — Flashback Gallery');
    }
}
