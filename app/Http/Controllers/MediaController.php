<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    public function show(string $path): StreamedResponse
    {
        $cleanPath = ltrim($path, '/');

        if (!Storage::disk('public')->exists($cleanPath)) {
            abort(404);
        }

        return Storage::disk('public')->response($cleanPath, null, [
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
