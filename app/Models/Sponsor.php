<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'name',
        'logo',
        'website',
        'contact_person',
        'email',
        'phone',
        'sponsorship_type',
        'amount',
        'description',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }
}
