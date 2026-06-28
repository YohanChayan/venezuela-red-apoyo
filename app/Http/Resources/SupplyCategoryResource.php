<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\SupplyCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SupplyCategory
 */
class SupplyCategoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => $this->icon,
            'color' => $this->color,
            'supplies' => $this->whenLoaded('supplies', fn () => $this->supplies->map(fn ($supply) => [
                'id' => $supply->id,
                'name' => $supply->name,
                'unit' => $supply->unit,
            ])->values()),
        ];
    }
}
