<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Building;
use App\Models\Need;
use App\Support\EnumOptions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Lightweight building shape for the public map/list. Requires the `needs`
 * relation to be loaded to compute the needs summary.
 *
 * @mixin Building
 */
class BuildingSummaryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $openNeeds = $this->needs
            ->filter(fn (Need $need) => $need->status->isOpen())
            ->sortByDesc(fn (Need $need) => $need->priority->weight());

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => EnumOptions::one($this->type),
            'status' => EnumOptions::one($this->status),
            'mode' => EnumOptions::one($this->mode),
            'structuralStatus' => EnumOptions::one($this->structural_status),
            'community' => $this->community ? [
                'name' => $this->community->name,
                'municipality' => $this->community->municipality,
                'state' => $this->community->state,
            ] : null,
            'address' => $this->address,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'peopleTrapped' => $this->people_trapped_estimate,
            'openNeedsCount' => $openNeeds->count(),
            'topNeeds' => $openNeeds->take(3)->map(fn (Need $need) => [
                'name' => $need->displayName(),
                'priority' => $need->priority->value,
            ])->values(),
            'confidence' => $this->confidence,
            'lastReportedAt' => $this->last_reported_at?->toIso8601String(),
        ];
    }
}
