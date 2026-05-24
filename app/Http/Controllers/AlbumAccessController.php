<?php

namespace App\Http\Controllers;

use App\Models\Album;

class AlbumAccessController extends Controller
{
    public function __invoke(string $token)
    {
        $album = Album::where('share_token', $token)->firstOrFail();

        if (! $album->is_public) {
            $unlocked = session('unlocked_albums', []);
            if (! in_array($album->id, $unlocked)) {
                $unlocked[] = $album->id;
                session(['unlocked_albums' => $unlocked]);
            }
        }

        return redirect()->route('albums.show', $album);
    }
}
