<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum DownloadCategory: string
{
    use HasOptions;

    case Form = 'form';
    case Regulation = 'regulation';
    case Educational = 'educational';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Form => 'فرم‌ها',
            self::Regulation => 'آیین‌نامه‌ها',
            self::Educational => 'جزوات آموزشی',
            self::Other => 'سایر',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Form => 'fa-file-signature',
            self::Regulation => 'fa-scale-balanced',
            self::Educational => 'fa-book-open',
            self::Other => 'fa-folder-open',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Form => 'فرم‌های ثبت‌نام، رضایت‌نامه و معرفی‌نامه',
            self::Regulation => 'آیین‌نامه‌های فنی، انضباطی و مقررات مسابقات',
            self::Educational => 'جزوه‌های فنی، تکنیک‌ها و منابع آزمون دان',
            self::Other => 'اسناد و فایل‌های متفرقه',
        };
    }
}
