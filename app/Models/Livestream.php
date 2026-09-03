<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Livestream extends Model
{
    use HasFactory;

    protected $fillable = ['tournament_id', 'added_by', 'platform', 'url', 'label', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}