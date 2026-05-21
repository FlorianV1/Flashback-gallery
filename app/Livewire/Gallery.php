<?php

namespace App\Livewire;

use App\Models\Album;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Flashback Gallery')]
class Gallery extends Component
{
    public ?int $unlockAlbumId = null;
    public string $unlockCode = '';
    public bool $unlockError = false;

    public function openUnlock(int $albumId): void
    {
        $this->unlockAlbumId = $albumId;
        $this->unlockCode = '';
        $this->unlockError = false;
    }

    public function closeUnlock(): void
    {
        $this->unlockAlbumId = null;
        $this->unlockCode = '';
        $this->unlockError = false;
    }

    public function submitUnlock(): void
    {
        $album = Album::find($this->unlockAlbumId);

        if (! $album) {
            $this->closeUnlock();
            return;
        }

        if ($album->checkCode($this->unlockCode)) {
            $unlocked = session('unlocked_albums', []);
            if (! in_array($album->id, $unlocked)) {
                $unlocked[] = $album->id;
                session(['unlocked_albums' => $unlocked]);
            }
            $this->redirect(route('albums.show', $album));
        } else {
            $this->unlockError = true;
            $this->unlockCode = '';
        }
    }

    public function render()
    {
        $albums = Album::query()
            ->orderBy('sort_order')
            ->orderByDesc('date_of_outing')
            ->with(['coverPhoto'])
            ->withCount('photos')
            ->get();

        return view('livewire.gallery', compact('albums'));
    }
}
