<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Key/value site settings — address, phone numbers, socials, working hours.
 *
 * Read on nearly every request (the footer alone uses a dozen), so the whole
 * table is cached as one map and busted on write.
 */
#[Fillable(['key', 'value', 'group', 'type'])]
class Setting extends Model
{
    use HasFactory;

    public const CACHE_KEY = 'settings.all';

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /**
     * The whole table as a key => value map.
     *
     * Deliberately not named all() — that would shadow Model::all() and break
     * every caller that expects a Collection, Filament's included.
     *
     * @return array<string, string|null>
     */
    public static function map(): array
    {
        return Cache::rememberForever(
            self::CACHE_KEY,
            fn () => static::query()->pluck('value', 'key')->all(),
        );
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::map()[$key] ?? $default;
    }

    public static function put(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
    }
}
