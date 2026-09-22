<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Stream a file from storage/app/public/{folder}/{filename}.
     * This is a safe, public endpoint intended as a fallback when the webserver
     * cannot serve the `public/storage` symlink directly.
     */
    public function storage(Request $request, $folder, $filename)
    {
        // Restrict allowed folders for safety
        if (!in_array($folder, ['article-images', 'featured-images', 'election-parties'], true) && strpos($folder, 'upload') === false) {
            abort(404);
        }

        // Serve from the public disk: storage/app/public/{folder}/{filename}
        // Some legacy uploads were stored under the default "local" disk (storage/app/private)
        // with a "public/" prefix (e.g. storage/app/private/public/featured-images/...).
        $candidatePaths = [
            "{$folder}/{$filename}",
            "public/{$folder}/{$filename}",
        ];

        foreach ($candidatePaths as $path) {
            if (Storage::disk('public')->exists($path)) {
                return response()->file(Storage::disk('public')->path($path));
            }
        }

        foreach ($candidatePaths as $path) {
            if (Storage::disk('local')->exists($path)) {
                return response()->file(Storage::disk('local')->path($path));
            }
        }

        abort(404);
    }
}
