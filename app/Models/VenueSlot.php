<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\SlotStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * سانس — one recurring weekly slot in one hall.
 *
 * day_of_week: 0 = شنبه … 6 = جمعه, the same index TrainingSession uses, so the
 * two timetables line up without a translation step.
 */
#[Fillable([
    'venue_id', 'training_class_id', 'day_of_week', 'start_time', 'end_time',
    'status', 'gender', 'holder', 'price', 'note',
])]
class VenueSlot extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'status' => SlotStatus::class,
            'gender' => Gender::class,
            'price' => 'integer',
        ];
    }

    /** @return BelongsTo<Venue, $this> */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /** @return BelongsTo<TrainingClass, $this> */
    public function trainingClass(): BelongsTo
    {
        return $this->belongsTo(TrainingClass::class);
    }

    /** @param Builder<$this> $query */
    public function scopeOnDay(Builder $query, int $day): void
    {
        $query->where('day_of_week', $day);
    }

    /** @param Builder<$this> $query */
    public function scopeToday(Builder $query): void
    {
        $query->where('day_of_week', TrainingSession::fromCarbonDay(now()->dayOfWeek));
    }

    /** @param Builder<$this> $query */
    public function scopeOpen(Builder $query): void
    {
        $query->where('status', SlotStatus::Open);
    }

    /** @param Builder<$this> $query */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('day_of_week')->orderBy('start_time');
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

    /** «۱ ساعت و ۳۰ دقیقه» — how long the hall is actually held. */
    protected function durationLabel(): Attribute
    {
        return Attribute::get(function (): string {
            $minutes = (int) round(abs(
                self::asTime($this->start_time)->diffInMinutes(self::asTime($this->end_time))
            ));

            $hours = intdiv($minutes, 60);
            $rest = $minutes % 60;

            return match (true) {
                $hours && $rest => fa($hours).' ساعت و '.fa($rest).' دقیقه',
                (bool) $hours => fa($hours).' ساعت',
                default => fa($rest).' دقیقه',
            };
        });
    }

    /** The slot's own price when it has one, otherwise the hall's rate. */
    protected function effectivePrice(): Attribute
    {
        return Attribute::get(fn (): int => $this->price ?? (int) $this->venue?->session_rate);
    }

    /**
     * Who is on the mat: the booking club, the class title, or the state itself.
     *
     * The class title is only read when the relation is already loaded — this
     * accessor is called once per cell on the hall board, and Model::shouldBeStrict()
     * would (rightly) turn a stray lazy load there into 40-odd queries.
     */
    protected function occupantLabel(): Attribute
    {
        return Attribute::get(fn (): string => $this->holder
            ?? ($this->relationLoaded('trainingClass') ? $this->trainingClass?->title : null)
            ?? $this->status->label());
    }

    /** True while this slot is running, for the «هم‌اکنون روی تاتامی» marker. */
    protected function isRunningNow(): Attribute
    {
        return Attribute::get(function (): bool {
            if ($this->day_of_week !== TrainingSession::fromCarbonDay(now()->dayOfWeek)) {
                return false;
            }

            $now = now()->format('H:i:s');

            return $now >= self::asTime($this->start_time)->format('H:i:s')
                && $now < self::asTime($this->end_time)->format('H:i:s');
        });
    }

    /**
     * Normalise a time column to a Carbon instance.
     *
     * The same `time` column comes back as «08:00:00» from MySQL, «08:00» from
     * SQLite (which the test suite uses) and occasionally as a DateTime — so
     * string comparison and fixed-format parsing both break on one driver or the
     * other. Everything that reasons about slot times goes through here.
     */
    public static function asTime(mixed $value): Carbon
    {
        return $value instanceof \DateTimeInterface
            ? Carbon::instance($value)
            : Carbon::parse((string) $value);
    }
}
