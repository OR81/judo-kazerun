<?php

namespace App\Models;

use App\Enums\DownloadCategory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** فرم‌ها، آیین‌نامه‌ها و جزوات آموزشی */
#[Fillable([
    'category', 'title', 'slug', 'description', 'file_path', 'file_name',
    'extension', 'size', 'downloads_count', 'is_active', 'order',
])]
class Download extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'category' => DownloadCategory::class,
            'size' => 'integer',
            'downloads_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @param Builder<$this> $query */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /** @param Builder<$this> $query */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('order')->orderBy('title');
    }

    protected function humanSize(): Attribute
    {
        return Attribute::get(function (): string {
            $kb = $this->size / 1024;

            return $kb >= 1024
                ? fa_number(round($kb / 1024, 1), 1).' مگابایت'
                : fa_number(max(1, round($kb))).' کیلوبایت';
        });
    }

    protected function icon(): Attribute
    {
        return Attribute::get(fn (): string => match (strtolower((string) $this->extension)) {
            'pdf' => 'fa-file-pdf',
            'doc', 'docx' => 'fa-file-word',
            'xls', 'xlsx' => 'fa-file-excel',
            'zip', 'rar' => 'fa-file-zipper',
            'jpg', 'jpeg', 'png' => 'fa-file-image',
            default => 'fa-file-lines',
        });
    }

    public function registerDownload(): void
    {
        $this->newQuery()->whereKey($this->getKey())->update([
            'downloads_count' => $this->downloads_count + 1,
        ]);
    }
}
