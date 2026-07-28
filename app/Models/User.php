<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Support\PhoneNumber;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * An account is a name, a role and a mobile number.
 *
 * There is no password and no email address: everyone signs in with a code texted
 * to the number, so the number is the identity and has to be stored in exactly one
 * shape — see the mobile mutator below.
 */
#[Fillable([
    'name', 'role', 'mobile', 'national_code',
    'birth_date', 'gender', 'avatar', 'city', 'is_active',
])]
#[Hidden(['remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'last_login_at' => 'datetime',
            'birth_date' => 'date',
            'is_active' => 'boolean',
            'role' => UserRole::class,
            'gender' => Gender::class,
        ];
    }

    /**
     * Store every number as 09xxxxxxxxx, whatever was typed.
     *
     * Sign-in looks the number up with a plain equality match, so a row saved as
     * «+98917…» from an import or an admin form would simply never be found.
     * Anything unparseable is stored as given and fails validation upstream rather
     * than being silently mangled here.
     */
    protected function mobile(): Attribute
    {
        return Attribute::set(fn (?string $value): ?string => $value === null
            ? null
            : (PhoneNumber::normalize($value) ?? $value));
    }

    /**
     * There is no password, but the session guard still asks for one.
     *
     * AuthenticateSession stores this value when a session starts and compares it
     * on every later request, so that changing a credential logs the account out
     * everywhere else. With nothing to change, a constant is the honest answer —
     * and it keeps the middleware from reading a column that no longer exists.
     */
    public function getAuthPassword(): string
    {
        return '';
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
