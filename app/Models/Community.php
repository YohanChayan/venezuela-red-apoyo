<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CommunityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Community extends Model
{
    /** @use HasFactory<CommunityFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'municipality',
        'state',
        'lat',
        'lng',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
        ];
    }

    /**
     * @return HasMany<Building, $this>
     */
    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }
}
