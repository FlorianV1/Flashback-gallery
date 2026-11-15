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

        return view('livewire.album-detail', [
            'photos' => $photos,
        ])->title($this->album->title.' — Flashback Gallery');
    }
}
