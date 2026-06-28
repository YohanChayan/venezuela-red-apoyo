<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Building;
use App\Support\EnumOptions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Full building detail for the show page.
 *
 * @mixin Building
 */
class BuildingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
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
            'peopleEvacuated' => $this->people_evacuated,
            'residents' => $this->residents_estimate,
            'contactName' => $this->contact_name,
            'contactPhone' => $this->contact_phone,
            'externalPersonsUrl' => $this->external_persons_url,
            'notes' => $this->notes,
            'notesUpdatedAt' => $this->notes_updated_at?->toIso8601String(),
            'sourceUrl' => $this->source_url,
            'confidence' => match ($this->confidence) {
                'high' => 'alta',
                'medium' => 'media',
                'low' => 'baja',
                default => $this->confidence,
            },
            'isLocked' => $this->is_locked,
            'statusIsManual' => $this->status_is_manual,
            'version' => $this->version,
            'createdAt' => $this->created_at?->toIso8601String(),
            'lastReportedAt' => $this->last_reported_at?->toIso8601String(),
            'lastReportedBy' => $this->last_reported_by,
            'needs' => NeedResource::collection($this->whenLoaded('needs')),
        ];
    }
}
