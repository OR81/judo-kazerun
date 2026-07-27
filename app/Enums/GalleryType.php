<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum GalleryType: string
{
    use HasOptions;

    case Photo = 'photo';
    case Video = 'video';

    public function label(): string
    {
        return match ($this) {
            self::Photo => 'تصاویر',
            self::Video => 'ویدئوها',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Photo => 'fa-images',
            self::Video => 'fa-video',
        };
    }
}
