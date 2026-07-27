<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\CarbonInterface;

#[Fillable([
    'user_id',
    'theme_id',
    'created_by',
    'name',
    'quote_text',
    'quote_source',
    'maps_link',
    'streaming_link',
    'max_guest',
    'expired_at',
    'status',
    'is_active',
    'published_at',
])]

/**
 * @property Carbon|null $expired_at
 * @property int|null $max_guest
 * @property bool $is_active
 */
class Invitation extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'max_guest' => 'integer',
            'expired_at' => 'datetime',
            'is_active' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------
    */

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Theme, $this>
     */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    // Admin yang membuatkan undangan ini (jika ada)
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return HasOne<Domain, $this>
     */
    public function domain(): HasOne
    {
        return $this->hasOne(Domain::class);
    }

    public function domainHistories(): HasMany
    {
        return $this->hasMany(DomainHistory::class);
    }

    public function couples(): HasMany
    {
        return $this->hasMany(InvitationCouple::class);
    }

    public function groom(): HasOne
    {
        return $this->hasOne(InvitationCouple::class)->where('type', 'groom');
    }

    public function bride(): HasOne
    {
        return $this->hasOne(InvitationCouple::class)->where('type', 'bride');
    }

    public function events(): HasMany
    {
        return $this->hasMany(InvitationEvent::class);
    }

    public function akad(): HasOne
    {
        return $this->hasOne(InvitationEvent::class)->where('type', 'akad');
    }

    public function resepsi(): HasOne
    {
        return $this->hasOne(InvitationEvent::class)->where('type', 'resepsi');
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(InvitationGallery::class)->orderBy('order');
    }

    public function coverPhotos(): HasMany
    {
        return $this->hasMany(InvitationGallery::class)->where('type', 'cover')->orderBy('order');
    }

    public function loveStories(): HasMany
    {
        return $this->hasMany(InvitationLoveStory::class)->orderBy('order');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(InvitationAccount::class);
    }

    public function giftAddress(): HasOne
    {
        return $this->hasOne(InvitationGiftAddress::class);
    }


    /**
     * @return HasOne<InvitationMusic, $this>
     */
    public function music(): HasOne
    {
        return $this->hasOne(InvitationMusic::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(InvitationGuest::class);
    }

    public function wishes(): HasMany
    {
        return $this->hasMany(InvitationWish::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /*
    |--------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------
    */

    public function isExpired(): bool
    {
        $expiredAt = $this->expired_at;

        if (! $expiredAt instanceof CarbonInterface) {
            return false;
        }

        return $expiredAt->isPast();
    }

    public function hasGuestLimit(): bool
    {
        return $this->max_guest !== null;
    }

    public function isGuestLimitReached(): bool
    {
        if (! $this->hasGuestLimit()) {
            return false;
        }

        return $this->guests()->count() >= $this->max_guest;
    }
}