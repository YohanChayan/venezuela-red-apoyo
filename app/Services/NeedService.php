<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\NeedStatus;
use App\Models\Building;
use App\Models\Contributor;
use App\Models\Need;
use App\Models\NeedCommitment;
use App\Models\Supply;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Owns the need lifecycle. The state machine now lives per commitment: each
 * person who takes charge advances their own status, and the need's overall
 * status is derived from the mix. Changes are serialized with a row lock so
 * concurrent volunteers don't clobber each other.
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
     * Record that a person commits to a need ("me encargo"). Many people can
     * commit to the same need, INCLUDING several from the same device — a
     * coordinator may anotar varias personas/brigadas. Commitments are keyed by
     * name (case-insensitive) within a need, so submitting the same name twice
     * is idempotent while distinct names each create a row. A fresh (or
     * previously cancelled) commitment starts at "comprometida".
     */
    public function commit(Need $need, Contributor $by, ?string $name = null): Need
    {
        return DB::transaction(function () use ($need, $by, $name): Need {
            /** @var Need $locked */
            $locked = Need::whereKey($need->getKey())->lockForUpdate()->firstOrFail();

            $trimmed = $name !== null ? trim($name) : '';

            if ($trimmed !== '') {
                // Identify by name: a new name adds a helper; the same name updates.
                $commitment = $locked->commitments()
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower($trimmed)])
                    ->first()
                    ?? $locked->commitments()->make(['contributor_id' => $by->id]);
                $commitment->name = $trimmed;
            } else {
                // Anonymous: keep a single anonymous row per device.
                $commitment = $locked->commitments()
                    ->where('contributor_id', $by->id)
                    ->whereNull('name')
                    ->first()
                    ?? $locked->commitments()->make(['contributor_id' => $by->id]);
            }

            // Joining (or re-joining after cancelling) puts the helper at the
            // start of their own lifecycle; an active commitment is untouched.
            if (! $commitment->exists || $commitment->status === NeedStatus::Cancelada) {
                $commitment->status = NeedStatus::Comprometida;
            }
            $commitment->save();

            $locked->claimed_by_contributor_id ??= $by->id;
            $locked->cancelled_at = null; // committing revives a cancelled need
            $this->syncStatus($locked);

            return $locked;
        });
    }

    /**
     * Cancel the WHOLE need: cancel every commitment and close the need so it
     * does not auto-reopen. Use when the request is no longer valid.
     */
    public function cancelNeed(Need $need): Need
    {
        return DB::transaction(function () use ($need): Need {
            /** @var Need $locked */
            $locked = Need::whereKey($need->getKey())->lockForUpdate()->firstOrFail();

            $locked->commitments()
                ->where('status', '!=', NeedStatus::Cancelada->value)
                ->update(['status' => NeedStatus::Cancelada->value]);

            $locked->cancelled_at = now();
            $this->syncStatus($locked);

            return $locked;
        });
    }

    /**
     * Reopen a cancelled need back to "solicitada" so people can take it on
     * again (cancelled commitments stay cancelled).
     */
    public function reopenNeed(Need $need): Need
    {
        return DB::transaction(function () use ($need): Need {
            /** @var Need $locked */
            $locked = Need::whereKey($need->getKey())->lockForUpdate()->firstOrFail();

            $locked->cancelled_at = null;
            $this->syncStatus($locked);

            return $locked;
        });
    }

    /**
     * Advance a single person's commitment through its lifecycle, enforcing
     * valid transitions, then recompute the need's overall status.
     *
     * @throws ValidationException on an invalid transition
     */
    public function transitionCommitment(NeedCommitment $commitment, NeedStatus $to, Contributor $by): NeedCommitment
    {
        return DB::transaction(function () use ($commitment, $to): NeedCommitment {
            /** @var NeedCommitment $locked */
            $locked = NeedCommitment::whereKey($commitment->getKey())->lockForUpdate()->firstOrFail();
            $from = $locked->status;

            if (! $from->canCommitmentTransitionTo($to)) {
                throw ValidationException::withMessages([
                    'status' => "Transición no permitida: {$from->label()} → {$to->label()}.",
                ]);
            }

            $locked->status = $to;
            $locked->save();

            /** @var Need $need */
            $need = Need::whereKey($locked->need_id)->lockForUpdate()->firstOrFail();
            $this->syncStatus($need);

            return $locked;
        });
    }

    /**
     * Recompute a need's overall status from the dynamic mix of its
     * commitments, keep its lifecycle timestamps coherent, and cascade to the
     * building semaphore. The stored status is a cache of the derived value.
     */
    private function syncStatus(Need $need): void
    {
        // A need-level cancellation overrides the per-commitment derivation.
        if ($need->cancelled_at !== null) {
            $need->status = NeedStatus::Cancelada;
            $need->last_reported_at = now();
            $need->save();
            $this->statusDeriver->apply($need->building);

            return;
        }

        $need->load('commitments');
        $status = $need->dominantStatus();

        $need->status = $status;
        $need->last_reported_at = now();

        match ($status) {
            NeedStatus::Comprometida => $need->claimed_at ??= now(),
            NeedStatus::Entregada => $need->fulfilled_at ??= now(),
            NeedStatus::Confirmada => $need->confirmed_at ??= now(),
            default => null,
        };

        $need->save();

        $this->statusDeriver->apply($need->building);
    }
}
