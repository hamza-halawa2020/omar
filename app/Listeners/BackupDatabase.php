<?php

namespace App\Listeners;

use App\Events\CreateBackup;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackupDatabase
{
    public function handle(CreateBackup $event): void
    {
        Artisan::call('backup:database');

        $disk = Storage::disk('local');

        $files = collect($disk->files('Laravel'))
            ->filter(fn ($f) => Str::endsWith($f, '.zip'))
            ->map(fn ($f) => [
                'path' => $f,
                'time' => $disk->lastModified($f),
            ])
            ->sortByDesc('time')
            ->values();

        $filesToDelete = $files->slice(50);

        foreach ($filesToDelete as $file) {
            $disk->delete($file['path']);
        }

    }
}
