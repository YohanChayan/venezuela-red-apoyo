<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BuildingMode;
use App\Enums\BuildingStatus;
use App\Enums\BuildingType;
use App\Enums\StructuralStatus;
use App\Models\Building;
use App\Models\Community;
use App\Models\Contributor;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Owns the lifecycle of buildings. Controllers stay thin; all write rules,
 * derived values and concurrency control live here (single responsibility).
 */
class BuildingService
{
    public function __construct(private readonly StatusDeriver $statusDeriver) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function register(array $data, Contributor $by): Building
    {
        $community = $this->resolveCommunity($data);
        $manualStatus = $this->manualStatus($data);

        $building = Building::create([
            ...$this->mapAttributes($data),
            'community_id' => $community?->id,
            'slug' => $this->uniqueSlug($data['name']),
            'status' => $manualStatus?->value ?? BuildingStatus::SinAsignar->value,
            'status_is_manual' => $manualStatus !== null,
            'notes_updated_at' => filled($data['notes'] ?? null) ? now() : null,
            'last_reported_at' => now(),
            'last_reported_by' => $by->name ?: 'Anónimo',
        ]);

        $this->statusDeriver->apply($building);

        return $building;
    }

    /**
     * Update a building with optimistic concurrency control: the caller must
     * send the version it last saw, or the edit is rejected.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws ValidationException on a stale version
     */
    public function update(Building $building, array $data, Contributor $by): Building
    {
        if ((int) ($data['version'] ?? -1) !== (int) $building->version) {
            throw ValidationException::withMessages([
                'version' => 'Alguien más actualizó este edificio mientras editabas. Recarga la página para ver los cambios.',
            ]);
        }

        $community = $this->resolveCommunity($data);
        $manualStatus = $this->manualStatus($data);

        $building->fill($this->mapAttributes($data));

        // The note is a single replaceable field — stamp it when it changes.
        if ($building->isDirty('notes') && filled($building->notes)) {
            $building->notes_updated_at = now();
        }

        $building->community_id = $community?->id ?? $building->community_id;
        $building->status_is_manual = $manualStatus !== null;
        if ($manualStatus !== null) {
            $building->status = $manualStatus->value;
        }
        $building->version = $building->version + 1;
        $building->last_reported_at = now();
        $building->last_reported_by = $by->name ?: 'Anónimo';
        $building->save();

        // Recomputes from needs/structure only when not manually overridden.
        $this->statusDeriver->apply($building);

        return $building;
    }

    /**
     * Resolves an explicit manual status, or null when the caller wants the
     * status auto-derived ('auto', empty, or absent).
     *
     * @param  array<string, mixed>  $data
     */
    private function manualStatus(array $data): ?BuildingStatus
    {
        $status = $data['status'] ?? null;

        if ($status === null || $status === '' || $status === 'auto') {
            return null;
        }

        return BuildingStatus::tryFrom((string) $status);
    }

    /**
     * Shared attribute mapping for create/update (slug and version excluded).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function mapAttributes(array $data): array
    {
        $structural = $data['structural_status'] ?? StructuralStatus::SinEvaluar->value;
        $trapped = (int) ($data['people_trapped_estimate'] ?? 0);

        // "Situación" is no longer asked: rescue is inferred from trapped people
        // or a damaged/collapsed structure; everything else is resupply.
        $isRescue = $trapped > 0 || in_array($structural, [
            StructuralStatus::Colapsado->value,
            StructuralStatus::Danado->value,
        ], true);

        return [
            'name' => $data['name'],
            'type' => ($data['type'] ?? null) ?: BuildingType::Otro->value,
            'mode' => $isRescue ? BuildingMode::Rescate->value : BuildingMode::Abastecimiento->value,
            'structural_status' => $structural,
            'address' => $data['address'] ?? null,
            'lat' => $data['lat'] ?? null,
            'lng' => $data['lng'] ?? null,
            'people_trapped_estimate' => $data['people_trapped_estimate'] ?? null,
            'residents_estimate' => $data['residents_estimate'] ?? null,
            'contact_name' => $data['contact_name'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? null,
            'notes' => $data['notes'] ?? null,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function resolveCommunity(array $data): ?Community
    {
        $name = $data['community_name'] ?? null;

        if (! is_string($name) || trim($name) === '') {
            return null;
        }

        return Community::firstOrCreate(
            ['slug' => Str::slug($name)],
            [
                'name' => $name,
                'municipality' => $data['municipality'] ?? null,
                'state' => $data['state'] ?? null,
            ],
        );
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'edificio';
        $slug = $base;
        $suffix = 2;

        while (Building::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
