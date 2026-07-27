<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** پیام‌های فرم تماس */
#[Fillable(['name', 'email', 'phone', 'subject', 'message', 'is_read', 'replied_at', 'ip_address'])]
class ContactMessage extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['is_read' => 'boolean', 'replied_at' => 'datetime'];
    }

    /** @param Builder<$this> $query */
    public function scopeUnread(Builder $query): void
    {
        $query->where('is_read', false);
    }
}
