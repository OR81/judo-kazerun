<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * جلسات هفتگی — day_of_week: 0 = شنبه … 6 = جمعه
 *
 * Carbon's dayOfWeek is 0 = Sunday, so conversion goes through toCarbonDay().
 */
#[Fillable(['training_class_id', 'day_of_week', 'start_time', 'end_time', 'venue'])]
class TrainingSession extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['day_of_week' => 'integer'];
    }

    /** @return BelongsTo<TrainingClass, $this> */
    public function trainingClass(): BelongsTo
    {
        return $this->belongsTo(TrainingClass::class);
    }

    /** Map a Carbon dayOfWeek (0 = Sunday) onto our index (0 = Saturday). */
    public static function fromCarbonDay(int $carbonDayOfWeek): int
    {
        return ($carbonDayOfWeek + 1) % 7;
    }

    /** @param Builder<$this> $query */
    public function scopeToday(Builder $query): void
    {
        $query->where('day_of_week', self::fromCarbonDay(now()->dayOfWeek));
    }

    /** @param Builder<$this> $query */
    public function scopeOnDay(Builder $query, ?int $day): void
    {
        if ($day !== null) {
            $query->where('day_of_week', $day);
        }
    }

    protected function dayName(): Attribute
    {
        return Attribute::get(fn (): string => weekday_fa($this->day_of_week));
    }

    /** «۱۷:۰۰ تا ۱۸:۳۰» */
    protected function timeRange(): Attribute
    {
        return Attribute::get(
            fn (): string => shamsi($this->start_time, 'time').' تا '.shamsi($this->end_time, 'time')
        );
    }
}
