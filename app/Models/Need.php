<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NeedPriority;
use App\Enums\NeedStatus;
use Database\Factories\NeedFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Need extends Model implements Auditable
{
    /** @use HasFactory<NeedFactory> */
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected array $auditExclude = [
        'version',
    ];

    protected $fillable = [
        'building_id',
        'supply_id',
        'supply_category_id',
        'custom_supply_name',
        'quantity',
        'unit',
        'priority',
        'status',
        'notes',
        'claimed_by_contributor_id',
        'claimed_at',
        'fulfilled_at',
        'confirmed_at',
        'created_by',
        'last_reported_at',
    ];

    protected function casts(): array
    {
        return [
            'priority' => NeedPriority::class,
            'status' => NeedStatus::class,
            'quantity' => 'float',
            'claimed_at' => 'datetime',
            'fulfilled_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'last_reported_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Building, $this>
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * @return BelongsTo<Supply, $this>
     */
    public function supply(): BelongsTo
    {
        return $this->belongsTo(Supply::class);
    }

    /**
     * @return BelongsTo<SupplyCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SupplyCategory::class, 'supply_category_id');
    }

    /**
     * @return BelongsTo<Contributor, $this>
     */
    public function claimedBy(): BelongsTo
    {
        return $this->belongsTo(Contributor::class, 'claimed_by_contributor_id');
    }

    /**
     * @return HasMany<NeedCommitment, $this>
     */
    public function commitments(): HasMany
    {
        return $this->hasMany(NeedCommitment::class);
    }

    /**
     * Human label for the requested supply: predefined name, or free-text.
     */
    public function displayName(): string
    {
        return $this->supply?->name
            ?? $this->custom_supply_name
            ?? 'Insumo sin especificar';
    }
}
