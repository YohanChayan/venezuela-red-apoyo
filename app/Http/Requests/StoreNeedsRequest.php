<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\NeedPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNeedsRequest extends FormRequest
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
            'needs' => ['required', 'array', 'min:1', 'max:50'],
            'needs.*.supply_id' => ['nullable', 'exists:supplies,id'],
            'needs.*.custom_supply_name' => ['nullable', 'required_without:needs.*.supply_id', 'string', 'max:255'],
            'needs.*.supply_category_id' => ['nullable', 'required_with:needs.*.custom_supply_name', 'exists:supply_categories,id'],
            'needs.*.quantity' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'needs.*.unit' => ['nullable', 'string', 'max:50'],
            'needs.*.priority' => ['required', Rule::enum(NeedPriority::class)],
            'needs.*.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'needs.*.custom_supply_name.required_without' => 'Cada insumo escrito a mano necesita un nombre.',
            'needs.*.supply_category_id.required_with' => 'Asigna una categoría a los insumos escritos a mano.',
        ];
    }
}
