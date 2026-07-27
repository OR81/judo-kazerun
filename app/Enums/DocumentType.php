<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/** مدارک لازم برای ثبت‌نام */
enum DocumentType: string
{
    use HasOptions;

    case NationalCard = 'national_card';
    case Photo = 'photo';
    case MedicalCertificate = 'medical_certificate';
    case BirthCertificate = 'birth_certificate';

    public function label(): string
    {
        return match ($this) {
            self::NationalCard => 'تصویر کارت ملی',
            self::Photo => 'عکس پرسنلی',
            self::MedicalCertificate => 'گواهی سلامت پزشکی',
            self::BirthCertificate => 'تصویر شناسنامه',
        };
    }

    public function hint(): string
    {
        return match ($this) {
            self::NationalCard => 'تصویر واضح از روی کارت ملی — JPG یا PNG، حداکثر ۲ مگابایت',
            self::Photo => 'عکس ۳×۴ با زمینهٔ روشن — JPG یا PNG، حداکثر ۲ مگابایت',
            self::MedicalCertificate => 'گواهی معتبر پزشک — JPG، PNG یا PDF، حداکثر ۴ مگابایت',
            self::BirthCertificate => 'صفحهٔ اول شناسنامه — JPG، PNG یا PDF، حداکثر ۴ مگابایت',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::NationalCard => 'fa-id-card',
            self::Photo => 'fa-image',
            self::MedicalCertificate => 'fa-notes-medical',
            self::BirthCertificate => 'fa-book',
        };
    }

    public function isRequired(): bool
    {
        return $this !== self::BirthCertificate;
    }

    /** Accepted upload types, mirrored by the server-side validation rules. */
    public function acceptedMimes(): array
    {
        return match ($this) {
            self::NationalCard, self::Photo => ['jpg', 'jpeg', 'png'],
            default => ['jpg', 'jpeg', 'png', 'pdf'],
        };
    }

    public function maxKilobytes(): int
    {
        return match ($this) {
            self::NationalCard, self::Photo => 2048,
            default => 4096,
        };
    }
}
