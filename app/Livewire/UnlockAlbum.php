<?php

namespace App\Livewire;

use App\Models\Album;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class UnlockAlbum extends Component
{
    public Album $album;

    #[Validate('required|string|max:8')]
    public string $code = '';

    public bool $error = false;

    public function mount(Album $album): void
    {
        if ($album->isUnlocked()) {
            $this->redirectRoute('albums.show', $album);
        }

        $this->album = $album;
    }

    public function unlock(): void
    {
        $this->validate();
        $this->error = false;

        if ($this->album->checkCode($this->code)) {
            $unlocked = session('unlocked_albums', []);

            if (! in_array($this->album->id, $unlocked)) {
                $unlocked[] = $this->album->id;
            }

            session(['unlocked_albums' => $unlocked]);

            $this->redirectRoute('albums.show', $this->album);

            return;
        }

        $this->error = true;
        $this->code = '';
    }

    public function render()
    {
        return view('livewire.unlock-album')
            ->title('Unlock Album — Flashback Gallery');
    }
}
