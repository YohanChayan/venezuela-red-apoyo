<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BuildingMode;
use App\Enums\BuildingStatus;
use App\Enums\BuildingType;
use App\Enums\StructuralStatus;
use Database\Factories\BuildingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Building extends Model implements Auditable
{
    /** @use HasFactory<BuildingFactory> */
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * Attributes excluded from the change history (noise / system fields).
     *
     * @var array<int, string>
     */
    protected array $auditExclude = [
        'version',
        'slug',
    ];

    protected $fillable = [
        'community_id',
        'name',
        'slug',
        'type',
        'address',
        'lat',
        'lng',
        'status',
        'status_is_manual',
        'mode',
        'structural_status',
        'people_trapped_estimate',
        'people_evacuated',
        'residents_estimate',
        'contact_name',
        'contact_phone',
        'external_persons_url',
        'notes',
        'source_url',
        'confidence',
        'is_locked',
        'locked_fields',
        'notes_updated_at',
        'last_reported_at',
        'last_reported_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => BuildingType::class,
            'status' => BuildingStatus::class,
            'mode' => BuildingMode::class,
            'structural_status' => StructuralStatus::class,
            'lat' => 'float',
            'lng' => 'float',
            'is_locked' => 'boolean',
            'status_is_manual' => 'boolean',
            'locked_fields' => 'array',
            'notes_updated_at' => 'datetime',
            'last_reported_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Community, $this>
     */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * @return HasMany<Need, $this>
     */
    public function needs(): HasMany
    {
        return $this->hasMany(Need::class);
    }

    /**
     * Whether a given field is frozen by an admin emergency-brake lock.
     */
    public function isFieldLocked(string $field): bool
    {
        return $this->is_locked || in_array($field, $this->locked_fields ?? [], true);
    }
}
