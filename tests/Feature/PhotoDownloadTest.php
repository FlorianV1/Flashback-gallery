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

it('downloads the whole album as one zip', function () {
    $album = makeAlbumWithPhotos(3);

    $response = $this->get(route('albums.download', $album));

    $response->assertOk();
    expect($response->headers->get('content-disposition'))->toContain('beach-trip-2025.zip');

    $tmp = tempnam(sys_get_temp_dir(), 'test-zip');
    file_put_contents($tmp, $response->streamedContent());

    $zip = new ZipArchive;
    expect($zip->open($tmp))->toBeTrue();
    expect($zip->numFiles)->toBe(3);
    expect($zip->getNameIndex(0))->toBe('pic1.jpg');
    expect($zip->getFromName('pic2.jpg'))->toBe('fake-jpeg-bytes-2');
    $zip->close();
    @unlink($tmp);
});

it('zips every photo, not just the first page', function () {
    $album = makeAlbumWithPhotos(60);

    $response = $this->get(route('albums.download', $album));
    $response->assertOk();

    $tmp = tempnam(sys_get_temp_dir(), 'test-zip');
    file_put_contents($tmp, $response->streamedContent());

    $zip = new ZipArchive;
    $zip->open($tmp);
    expect($zip->numFiles)->toBe(60);
    $zip->close();
    @unlink($tmp);
});

it('refuses the zip for a locked album', function () {
    $album = makeAlbumWithPhotos(2, ['is_public' => false, 'access_code' => 'secret']);

    $this->get(route('albums.download', $album))->assertForbidden();
});
