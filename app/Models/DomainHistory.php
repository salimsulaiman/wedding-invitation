<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['invitation_id', 'domain_id', 'old_name', 'new_name', 'changed_by', 'note'])]
class DomainHistory extends Model
{
    use HasFactory;

    public const UPDATED_AT = null; // tabel ini cuma insert log, gak ada updated_at

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}