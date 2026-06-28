<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SupplyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supply extends Model
{
    /** @use HasFactory<SupplyFactory> */
    use HasFactory;

    protected $fillable = [
        'supply_category_id',
        'name',
        'slug',
        'unit',
        'is_predefined',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_predefined' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<SupplyCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SupplyCategory::class, 'supply_category_id');
    }
}
