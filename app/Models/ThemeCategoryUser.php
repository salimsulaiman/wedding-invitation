<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'theme_category_id', 'granted_by', 'granted_at'])]
class ThemeCategoryUser extends Model
{
    use HasFactory;

    protected $table = 'theme_category_user';

    protected function casts(): array
    {
        return [
            'granted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function themeCategory(): BelongsTo
    {
        return $this->belongsTo(ThemeCategory::class);
    }

    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }
}