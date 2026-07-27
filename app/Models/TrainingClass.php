<?php

namespace App\Models;

use App\Enums\AgeGroup;
use App\Enums\ClassLevel;
use App\Enums\Gender;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** کلاس‌های تمرینی */
#[Fillable([
    'coach_id', 'title', 'slug', 'age_group', 'gender', 'level',
    'capacity', 'enrolled_count', 'monthly_fee', 'venue',
    'description', 'is_active', 'order',
])]
class TrainingClass extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'age_group' => AgeGroup::class,
            'gender' => Gender::class,
            'level' => ClassLevel::class,
            'capacity' => 'integer',
            'enrolled_count' => 'integer',
            'monthly_fee' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return BelongsTo<Coach, $this> */
    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    /** @return HasMany<TrainingSession, $this> */
    public function sessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class)
            ->orderBy('day_of_week')
            ->orderBy('start_time');
    }

    /** @return HasMany<Enrollment, $this> */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
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

    protected function remainingSeats(): Attribute
    {
        return Attribute::get(fn (): int => max(0, $this->capacity - $this->enrolled_count));
    }

    protected function isFull(): Attribute
    {
        return Attribute::get(fn (): bool => $this->remaining_seats === 0);
    }

    /** 0..100, for the capacity bar. */
    protected function occupancyPercent(): Attribute
    {
        return Attribute::get(fn (): int => $this->capacity > 0
            ? (int) min(100, round($this->enrolled_count / $this->capacity * 100))
            : 0);
    }

    /** Green while there's room, amber when nearly full, brand red when full. */
    protected function capacityTone(): Attribute
    {
        return Attribute::get(function (): string {
            $percent = $this->occupancy_percent;

            return match (true) {
                $percent >= 100 => 'bg-brand',
                $percent >= 80 => 'bg-accent',
                default => 'bg-emerald-500',
            };
        });
    }

    /** «شنبه و دوشنبه — ۱۷:۰۰ تا ۱۸:۳۰» */
    protected function scheduleSummary(): Attribute
    {
        return Attribute::get(function (): string {
            $sessions = $this->sessions;

            if ($sessions->isEmpty()) {
                return 'زمان‌بندی اعلام نشده';
            }

            $days = $sessions->map(fn (TrainingSession $s) => weekday_fa($s->day_of_week))
                ->unique()
                ->implode(' و ');

            $first = $sessions->first();

            return $days.' — '.shamsi($first->start_time, 'time').' تا '.shamsi($first->end_time, 'time');
        });
    }
}
