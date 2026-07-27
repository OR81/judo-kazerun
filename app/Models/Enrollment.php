<?php

namespace App\Models;

use App\Enums\EnrollmentStatus;
use App\Enums\Gender;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** ثبت‌نام‌ها */
#[Fillable([
    'user_id', 'training_class_id', 'reference', 'first_name', 'last_name',
    'national_code', 'mobile', 'email', 'birth_date', 'gender',
    'guardian_name', 'emergency_phone', 'address', 'medical_notes',
    'has_insurance', 'amount', 'status', 'admin_notes', 'approved_at',
])]
class Enrollment extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'approved_at' => 'datetime',
            'gender' => Gender::class,
            'status' => EnrollmentStatus::class,
            'has_insurance' => 'boolean',
            'amount' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    protected static function booted(): void
    {
        static::creating(function (self $enrollment) {
            $enrollment->reference ??= self::generateReference();
        });
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<TrainingClass, $this> */
    public function trainingClass(): BelongsTo
    {
        return $this->belongsTo(TrainingClass::class);
    }

    /** @return HasMany<EnrollmentDocument, $this> */
    public function documents(): HasMany
    {
        return $this->hasMany(EnrollmentDocument::class);
    }

    /** @return HasMany<Transaction, $this> */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /** @param Builder<$this> $query */
    public function scopePending(Builder $query): void
    {
        $query->where('status', EnrollmentStatus::Pending);
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => trim("{$this->first_name} {$this->last_name}"));
    }

    /**
     * Tracking code shown on the receipt, e.g. «KJ-7H4KP92R».
     *
     * The alphabet drops 0/O/1/I/L so the code survives being read aloud over
     * the phone or copied off a printout.
     */
    protected static function generateReference(): string
    {
        $alphabet = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

        do {
            $code = '';
            for ($i = 0; $i < 8; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $reference = 'KJ-'.$code;
        } while (static::query()->where('reference', $reference)->exists());

        return $reference;
    }
}
