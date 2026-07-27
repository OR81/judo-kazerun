<?php

declare(strict_types=1);

namespace App\Enums\Concerns;

/**
 * Shared helpers for the backed enums that describe domain vocabulary.
 *
 * Every enum here carries its own Persian label so the wording lives in one
 * place and stays identical across Blade, Filament and validation messages.
 */
trait HasOptions
{
    /** ['value' => 'برچسب فارسی'] — ready for a <select> or a Filament field. */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    /** Raw values, for validation rules. */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function tryLabel(?string $value): string
    {
        return $value ? (self::tryFrom($value)?->label() ?? $value) : '—';
    }
}
