<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public function storePublicFile(UploadedFile $file, string $directory): string
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path($directory), $filename);

        return trim($directory, '/') . '/' . $filename;
    }

    public function deletePublicFile(?string $relativePath): void
    {
        if ($relativePath && file_exists(public_path($relativePath))) {
            unlink(public_path($relativePath));
        }
    }

    public function storeStorageFile(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        return $file->store($directory, $disk);
    }

    public function deleteStorageFile(?string $path, string $disk = 'public'): void
    {
        if ($path) {
            Storage::disk($disk)->delete($path);
        }
    }
}
