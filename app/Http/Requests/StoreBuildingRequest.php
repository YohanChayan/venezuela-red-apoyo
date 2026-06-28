<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\BuildingStatus;
use App\Enums\BuildingType;
use App\Enums\StructuralStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBuildingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', Rule::enum(BuildingType::class)],
            'status' => ['nullable', Rule::in([...array_column(BuildingStatus::cases(), 'value'), 'auto'])],
            'structural_status' => ['nullable', Rule::enum(StructuralStatus::class)],

            'community_name' => ['nullable', 'string', 'max:255'],
            'municipality' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],

            'residents_estimate' => ['nullable', 'integer', 'min:0', 'max:1000000'],

            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'type' => 'tipo',
            'community_name' => 'comunidad',
            'contact_phone' => 'teléfono de contacto',
        ];
    }
}
