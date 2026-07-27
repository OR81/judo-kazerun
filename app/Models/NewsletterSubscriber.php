<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/** مشترکان خبرنامه */
#[Fillable(['email', 'token', 'confirmed_at', 'unsubscribed_at'])]
class NewsletterSubscriber extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['confirmed_at' => 'datetime', 'unsubscribed_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (self $subscriber) {
            $subscriber->token ??= Str::random(64);
        });
    }

    public function getRouteKeyName(): string
    {
        return 'token';
    }

    /** @param Builder<$this> $query */
    public function scopeActive(Builder $query): void
    {
        $query->whereNull('unsubscribed_at');
    }
}
