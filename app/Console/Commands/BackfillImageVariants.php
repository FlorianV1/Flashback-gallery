<?php

namespace App\Console\Commands;

use App\Models\Photo;
use App\Services\ImageProcessor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackfillImageVariants extends Command
{
    protected $signature = 'photos:backfill-variants {--force : Regenerate even if variants already exist}';

    protected $description = 'Generate thumbnail and web variants for photos that are missing them';

    public function handle(ImageProcessor $processor): int
    {
        $force = $this->option('force');

        $photos = Photo::all();
        $total = $photos->count();

        if ($total === 0) {
            $this->info('No photos found.');
            return self::SUCCESS;
        }

        $this->info("Processing {$total} photos...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $generated = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($photos as $photo) {
            $bar->advance();

            $dir = dirname($photo->filename);
            $base = pathinfo($photo->filename, PATHINFO_FILENAME);
            $thumbPath = $dir . '/thumbs/' . $base . '.jpg';

            if (! $force && Storage::disk('public')->exists($thumbPath)) {
                $skipped++;
                continue;
            }

            if (! Storage::disk('public')->exists($photo->filename)) {
                $this->newLine();
                $this->warn("  Skipping #{$photo->id} — original file missing: {$photo->filename}");
                $failed++;
                continue;
            }

            try {
                $processor->generateVariants($photo->filename);
                $generated++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->error("  Failed #{$photo->id} ({$photo->original_filename}): {$e->getMessage()}");
                $failed++;
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Done. Generated: {$generated} | Skipped: {$skipped} | Failed: {$failed}");

        return self::SUCCESS;
    }
}
