<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\NeedStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNeedStatusRequest extends FormRequest
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
            'status' => ['required', Rule::enum(NeedStatus::class)],
        ];
    }

    public function targetStatus(): NeedStatus
    {
        return NeedStatus::from($this->string('status')->value());
    }
}
