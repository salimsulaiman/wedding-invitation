<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['theme_category_id', 'name', 'slug', 'component_key', 'thumbnail', 'description', 'is_active'])]
class Theme extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ThemeCategory::class, 'theme_category_id');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }
}