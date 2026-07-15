<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['invitation_id', 'type', 'photo', 'caption', 'order'])]
class InvitationGallery extends Model
{
    use HasFactory;

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}