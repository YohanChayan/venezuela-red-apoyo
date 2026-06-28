<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SupplyCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplyCategory extends Model
{
    /** @use HasFactory<SupplyCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'sort_order',
    ];

    /**
     * @return HasMany<Supply, $this>
     */
    public function supplies(): HasMany
    {
        return $this->hasMany(Supply::class);
    }
}
