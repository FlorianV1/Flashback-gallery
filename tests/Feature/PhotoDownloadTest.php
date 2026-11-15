<?php

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(fn () => Storage::fake('public'));

function makeAlbumWithPhotos(int $count = 2, array $attrs = []): Album
{
    $album = Album::create(array_merge([
        'title' => 'Beach Trip 2025',
        'date_of_outing' => '2025-07-04',
        'is_public' => true,
    ], $attrs));

    foreach (range(1, $count) as $i) {
        $name = "photos/pic{$i}.jpg";
        Storage::disk('public')->put($name, "fake-jpeg-bytes-{$i}");

        Photo::create([
            'album_id' => $album->id,
            'filename' => $name,
            'original_filename' => "pic{$i}.jpg",
            'type' => 'image',
            'sort_order' => $i,
        ]);
    }

    return $album;
}

it('downloads a single photo with its real name', function () {
    $album = makeAlbumWithPhotos(1);
    $photo = $album->photos()->first();

    $response = $this->get(route('photos.download', [$album, $photo]));

    $response->assertOk();
    expect($response->headers->get('content-disposition'))->toContain('pic1.jpg');
});

it('refuses a photo from a locked album', function () {
    $album = makeAlbumWithPhotos(1, ['is_public' => false, 'access_code' => 'secret']);
    $photo = $album->photos()->first();

    $this->get(route('photos.download', [$album, $photo]))->assertForbidden();
});
