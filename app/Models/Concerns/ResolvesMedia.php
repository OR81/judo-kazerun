<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Resolves an image column to a usable URL.
 *
 * Seed data points at remote stock photography while there are no real photos.
 * Once the board uploads its own through the admin panel the column holds a
 * relative disk path instead — both work, with no change at the call site.
 */
trait ResolvesMedia
{
    protected function mediaUrl(?string $path, ?string $fallback = null): ?string
    {
        if (blank($path)) {
            return $fallback;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//', 'data:'])) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}
