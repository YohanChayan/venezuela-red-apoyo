<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\NeedPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNeedRequest extends FormRequest
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
            'supply_id' => ['nullable', 'exists:supplies,id'],
            // Free-text supply: required only when no predefined supply is chosen.
            'custom_supply_name' => ['nullable', 'required_without:supply_id', 'string', 'max:255'],
            'supply_category_id' => ['nullable', 'required_with:custom_supply_name', 'exists:supply_categories,id'],
            'quantity' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'unit' => ['nullable', 'string', 'max:50'],
            'priority' => ['required', Rule::enum(NeedPriority::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'custom_supply_name.required_without' => 'Elige un insumo de la lista o escribe uno.',
            'supply_category_id.required_with' => 'Asigna una categoría al insumo que escribiste.',
        ];
    }
}
