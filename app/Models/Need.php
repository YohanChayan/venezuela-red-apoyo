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
use Illuminate\Support\Collection;
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
        'last_reported_at',
        'claimed_at',
        'fulfilled_at',
        'confirmed_at',
        'cancelled_at',
        'claimed_by_contributor_id',
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
        'cancelled_at',
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
            'cancelled_at' => 'datetime',
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

    /**
     * Commitments that still count toward the need's progress (cancelled
     * commitments mean that person backed out and are ignored).
     *
     * @return Collection<int, NeedCommitment>
     */
    public function activeCommitments(): Collection
    {
        $commitments = $this->relationLoaded('commitments')
            ? $this->commitments
            : $this->commitments()->get();

        return $commitments->reject(
            fn (NeedCommitment $commitment) => $commitment->status === NeedStatus::Cancelada,
        )->values();
    }

    /**
     * The need's overall status, derived dynamically from its commitments: the
     * status held by the most people wins, ties broken by the most advanced
     * status. No active commitments means the need is still "solicitada".
     */
    public function dominantStatus(): NeedStatus
    {
        $active = $this->activeCommitments();

        if ($active->isEmpty()) {
            return NeedStatus::Solicitada;
        }

        $counts = $active->countBy(fn (NeedCommitment $commitment) => $commitment->status->value);
        $max = $counts->max();

        return collect(NeedStatus::cases())
            ->filter(fn (NeedStatus $status) => ($counts->get($status->value, 0)) === $max)
            ->sortByDesc(fn (NeedStatus $status) => $status->order())
            ->first();
    }

    /**
     * How many people sit in each lifecycle status, ordered along the
     * lifecycle. Only statuses with at least one person are returned.
     *
     * @return array<int, array{status: NeedStatus, count: int}>
     */
    public function commitmentStatusCounts(): array
    {
        $counts = $this->activeCommitments()
            ->countBy(fn (NeedCommitment $commitment) => $commitment->status->value);

        return collect(NeedStatus::cases())
            ->filter(fn (NeedStatus $status) => $counts->get($status->value, 0) > 0)
            ->map(fn (NeedStatus $status) => [
                'status' => $status,
                'count' => $counts->get($status->value),
            ])
            ->values()
            ->all();
    }
}
