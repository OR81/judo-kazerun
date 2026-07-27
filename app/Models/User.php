<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name', 'email', 'password', 'role', 'mobile', 'national_code',
    'birth_date', 'gender', 'avatar', 'city', 'is_active',
])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'birth_date' => 'date',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'role' => UserRole::class,
            'gender' => Gender::class,
        ];
    }

    /**
     * Only administrators reach the Filament panel. Athletes and coaches have
     * their own Blade dashboards that match the public design system.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === UserRole::Admin && $this->is_active;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isCoach(): bool
    {
        return $this->role === UserRole::Coach;
    }

    public function isAthlete(): bool
    {
        return $this->role === UserRole::Athlete;
    }

    /** @return HasOne<Coach, $this> */
    public function coach(): HasOne
    {
        return $this->hasOne(Coach::class);
    }

    /** @return HasOne<Athlete, $this> */
    public function athlete(): HasOne
    {
        return $this->hasOne(Athlete::class);
    }

    /** @return HasMany<Enrollment, $this> */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/u', trim($this->name)) ?: [];

        return mb_substr($parts[0] ?? '', 0, 1).mb_substr($parts[1] ?? '', 0, 1);
    }
}
