<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    use HasFactory;

    /**
     * Fields that may be filled through create or update operations.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'genre',
        'platform',
        'rules',
        'team_size',
    ];

    /**
     * All tournaments created under this game.
     */
    public function tournaments(): HasMany
    {
        return $this->hasMany(Tournament::class);
    }

    /**
     * Convert database values into appropriate PHP types.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'team_size' => 'integer',
        ];
    }
}