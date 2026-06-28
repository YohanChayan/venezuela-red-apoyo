<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BuildingStatus;
use App\Enums\NeedPriority;
use App\Enums\StructuralStatus;
use App\Models\Building;
use App\Models\Need;

/**
 * Derives a building's semaphore status from its open needs and structural
 * condition. Keeps "alarm level" in sync with reality automatically; a manual
 * override bypasses this (status_is_manual). The baseline for an unevaluated
 * place is "necesita apoyo".
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
            $building->structural_status === StructuralStatus::Colapsado
            || $hasCritical
        ) {
            return BuildingStatus::Critico;
        }

        if ($building->structural_status === StructuralStatus::Seguro && $openNeeds->isEmpty()) {
            return BuildingStatus::Normal;
        }

        return BuildingStatus::NecesitaApoyo;
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
