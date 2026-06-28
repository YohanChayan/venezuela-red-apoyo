<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BuildingMode;
use App\Enums\BuildingStatus;
use App\Enums\NeedPriority;
use App\Enums\StructuralStatus;
use App\Models\Building;
use App\Models\Need;

/**
 * Derives a building's semaphore status from its open needs, structural
 * condition and trapped-people estimate. Keeps "alarm level" in sync with
 * reality automatically; a manual override bypasses this (status_is_manual).
 */
class StatusDeriver
{
    public function derive(Building $building): BuildingStatus
    {
        $openNeeds = $building->needs->filter(fn (Need $need) => $need->status->isOpen());

        $hasCritical = $openNeeds->contains(
            fn (Need $need) => $need->priority === NeedPriority::Critica,
        );

        if (
            ($building->people_trapped_estimate ?? 0) > 0
            || $building->structural_status === StructuralStatus::Colapsado
            || $hasCritical
        ) {
            return BuildingStatus::Critico;
        }

        if (
            $openNeeds->isNotEmpty()
            || $building->structural_status === StructuralStatus::Danado
            || $building->mode === BuildingMode::Rescate
        ) {
            return BuildingStatus::NecesitaApoyo;
        }

        if ($building->structural_status === StructuralStatus::Seguro) {
            return BuildingStatus::Normal;
        }

        return BuildingStatus::SinAsignar;
    }

    /**
     * Recompute and persist the status unless it was set manually.
     */
    public function apply(Building $building): Building
    {
        if ($building->status_is_manual) {
            return $building;
        }

        $building->load('needs');
        $building->status = $this->derive($building);
        $building->save();

        return $building;
    }
}
