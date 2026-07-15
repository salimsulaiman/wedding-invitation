<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'invitation_id',
    'name',
    'phone',
    'group',
    'slug',
    'quota',
    'is_sent',
    'opened_at',
])]
class InvitationGuest extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'quota' => 'integer',
            'is_sent' => 'boolean',
            'opened_at' => 'datetime',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function wishes(): HasMany
    {
        return $this->hasMany(InvitationWish::class, 'guest_id');
    }
}