<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'is_active',
        'must_change_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function dashboardRoute(): string
    {
        return $this->isAdmin()
            ? route('admin.dashboard')
            : route('user.dashboard');
    }

    /*
    |--------------------------------------------------------------------------
    | Customer
    |--------------------------------------------------------------------------
    */

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function accessibleThemeCategories(): BelongsToMany
    {
        return $this->belongsToMany(ThemeCategory::class, 'theme_category_user')
            ->withPivot(['granted_by', 'granted_at'])
            ->withTimestamps();
    }

    public function accessibleThemes()
    {
        return Theme::whereIn(
            'theme_category_id',
            $this->accessibleThemeCategories()->pluck('theme_categories.id')
        )
            ->where('is_active', true)
            ->get();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */

    public function createdInvitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'created_by');
    }

    public function grantedThemeCategoryAccess(): HasMany
    {
        return $this->hasMany(ThemeCategoryUser::class, 'granted_by');
    }

    public function handledOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'handled_by');
    }
}