<?php

namespace App\Models;

use App\Enums\CompetitionLevel;
use App\Enums\MedalRank;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** افتخارات و مدال‌های ورزشکاران */
#[Fillable([
    'athlete_id', 'title', 'competition', 'rank', 'level',
    'year', 'achieved_at', 'description',
])]
class Achievement extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'rank' => MedalRank::class,
            'level' => CompetitionLevel::class,
            'year' => 'integer',
            'achieved_at' => 'date',
        ];
    }

    /** @return BelongsTo<Athlete, $this> */
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    /** @param Builder<$this> $query */
    public function scopeMedals(Builder $query): void
    {
        $query->whereIn('rank', [
            MedalRank::Gold->value,
            MedalRank::Silver->value,
            MedalRank::Bronze->value,
        ]);
    }
}
