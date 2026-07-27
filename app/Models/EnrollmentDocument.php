<?php

namespace App\Models;

use App\Enums\DocumentType;
use App\Models\Concerns\ResolvesMedia;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/** مدارک بارگذاری‌شدهٔ ثبت‌نام */
#[Fillable(['enrollment_id', 'type', 'path', 'original_name', 'size', 'mime'])]
class EnrollmentDocument extends Model
{
    use HasFactory, ResolvesMedia;

    protected function casts(): array
    {
        return ['type' => DocumentType::class, 'size' => 'integer'];
    }

    protected static function booted(): void
    {
        // Don't leave orphaned uploads behind on the disk.
        static::deleted(function (self $document) {
            if ($document->path && ! str_starts_with($document->path, 'http')) {
                Storage::disk('public')->delete($document->path);
            }
        });
    }

    /** @return BelongsTo<Enrollment, $this> */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    protected function url(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->mediaUrl($this->path));
    }

    protected function humanSize(): Attribute
    {
        return Attribute::get(function (): string {
            $kb = $this->size / 1024;

            return $kb >= 1024
                ? fa_number(round($kb / 1024, 1), 1).' مگابایت'
                : fa_number(round($kb)).' کیلوبایت';
        });
    }
}
