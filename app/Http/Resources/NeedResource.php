<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Need;
use App\Support\EnumOptions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Need
 */
class NeedResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // The overall status is derived dynamically from the mix of per-person
        // commitments, not read from the stored column.
        $status = $this->dominantStatus();

        return [
            'id' => $this->id,
            'name' => $this->displayName(),
            'category' => $this->whenLoaded('category', fn () => $this->category ? [
                'name' => $this->category->name,
                'icon' => $this->category->icon,
                'color' => $this->category->color,
            ] : null),
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'priority' => EnumOptions::one($this->priority),
            'status' => EnumOptions::one($status),
            'isOpen' => $status->isOpen(),
            'notes' => $this->notes,
            'createdBy' => $this->created_by,
            'claimedBy' => $this->claimedBy?->name,
            'claimedAt' => $this->claimed_at?->toIso8601String(),
            'commitmentsCount' => $this->whenLoaded('commitments', fn () => $this->activeCommitments()->count(), 0),
            'statusCounts' => $this->whenLoaded('commitments', fn () => collect($this->commitmentStatusCounts())
                ->map(fn (array $entry) => [
                    ...EnumOptions::one($entry['status']),
                    'count' => $entry['count'],
                ])->all()),
            'commitments' => $this->whenLoaded('commitments', fn () => $this->commitments
                ->sortByDesc('created_at')
                ->map(fn ($commitment) => [
                    'id' => $commitment->id,
                    'name' => $commitment->name ?: 'Anónimo',
                    'status' => EnumOptions::one($commitment->status),
                    'allowedTransitions' => EnumOptions::from($commitment->status->commitmentTransitions()),
                    'at' => $commitment->created_at?->toIso8601String(),
                ])->values()),
            'createdAt' => $this->created_at?->toIso8601String(),
            'lastReportedAt' => $this->last_reported_at?->toIso8601String(),
        ];
    }
}
