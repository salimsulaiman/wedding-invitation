<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'price', 'description', 'allow_custom_photos', 'is_active'])]
class ThemeCategory extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'allow_custom_photos' => 'boolean',
        ];
    }

    public function themes(): HasMany
    {
        return $this->hasMany(Theme::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Customer yang sudah dibukakan akses ke paket ini oleh admin
    public function usersWithAccess(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'theme_category_user')
            ->withPivot(['granted_by', 'granted_at'])
            ->withTimestamps();
    }
}