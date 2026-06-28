<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\NeedStatus;
use App\Models\Building;
use App\Models\Contributor;
use App\Models\Need;
use App\Models\Supply;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Owns the need lifecycle: creation and the anti-chaos state machine. Status
 * changes are serialized with a row lock so two volunteers can't claim the
 * same need.
 */
class NeedService
{
    public function __construct(private readonly StatusDeriver $statusDeriver) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function add(Building $building, array $data, Contributor $by): Need
    {
        $need = $this->createNeed($building, $data, $by);

        $this->statusDeriver->apply($building);

        return $need;
    }

    /**
     * Add several needs in one transaction, then recompute status once.
     *
     * @param  array<int, array<string, mixed>>  $items
     */
    public function addMany(Building $building, array $items, Contributor $by): int
    {
        $count = DB::transaction(function () use ($building, $items, $by): int {
            foreach ($items as $item) {
                $this->createNeed($building, $item, $by);
            }

            return count($items);
        });

        $this->statusDeriver->apply($building);

        return $count;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function createNeed(Building $building, array $data, Contributor $by): Need
    {
        $supply = isset($data['supply_id']) ? Supply::find($data['supply_id']) : null;

        return $building->needs()->create([
            'supply_id' => $supply?->id,
            'custom_supply_name' => $supply ? null : ($data['custom_supply_name'] ?? null),
            'supply_category_id' => $supply?->supply_category_id ?? ($data['supply_category_id'] ?? null),
            'quantity' => $data['quantity'] ?? null,
            'unit' => $data['unit'] ?? $supply?->unit,
            'priority' => $data['priority'],
            'status' => NeedStatus::Solicitada->value,
            'notes' => $data['notes'] ?? null,
            'created_by' => $by->name ?: 'Anónimo',
            'last_reported_at' => now(),
        ]);
    }

    /**
     * Move a need to a new lifecycle state, enforcing valid transitions.
     *
     * @throws ValidationException on an invalid transition
     */
    public function transition(Need $need, NeedStatus $to, Contributor $by): Need
    {
        return DB::transaction(function () use ($need, $to, $by): Need {
            /** @var Need $locked */
            $locked = Need::whereKey($need->getKey())->lockForUpdate()->firstOrFail();
            $from = $locked->status;

            if (! $from->canTransitionTo($to)) {
                throw ValidationException::withMessages([
                    'status' => "Transición no permitida: {$from->label()} → {$to->label()}.",
                ]);
            }

            $locked->status = $to;
            $locked->last_reported_at = now();

            match ($to) {
                NeedStatus::Comprometida => $locked->forceFill([
                    'claimed_by_contributor_id' => $by->id,
                    'claimed_at' => now(),
                ]),
                NeedStatus::Entregada => $locked->forceFill(['fulfilled_at' => now()]),
                NeedStatus::Confirmada => $locked->forceFill(['confirmed_at' => now()]),
                default => $locked,
            };

            $locked->save();

            $this->statusDeriver->apply($locked->building);

            return $locked;
        });
    }

    /**
     * Record that a person commits to a need ("me encargo"). Many people can
     * commit to the same need; one commitment per contributor.
     */
    public function commit(Need $need, Contributor $by, ?string $name = null): Need
    {
        return DB::transaction(function () use ($need, $by, $name): Need {
            /** @var Need $locked */
            $locked = Need::whereKey($need->getKey())->lockForUpdate()->firstOrFail();

            $commitment = $locked->commitments()->firstOrNew(['contributor_id' => $by->id]);
            if ($name !== null && trim($name) !== '') {
                $commitment->name = trim($name);
            } elseif (! $commitment->exists) {
                $commitment->name = $by->name;
            }
            $commitment->save();

            // The first commitment moves the need into "Asignada" (comprometida).
            if ($locked->status === NeedStatus::Solicitada) {
                $locked->status = NeedStatus::Comprometida;
                $locked->claimed_by_contributor_id ??= $by->id;
                $locked->claimed_at ??= now();
            }

            $locked->last_reported_at = now();
            $locked->save();

            $this->statusDeriver->apply($locked->building);

            return $locked;
        });
    }
}
