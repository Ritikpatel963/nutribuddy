<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class StorageController extends Controller
{
    public function showPublic(string $path)
    {
        $normalizedPath = str_replace('\\', '/', $path);

        abort_if(
            str_contains($normalizedPath, '../')
            || str_starts_with($normalizedPath, '/'),
            404
        );

        $disk = Storage::disk('public');

        abort_unless($disk->exists($normalizedPath), 404);

        return response()->file($disk->path($normalizedPath), [
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
