<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteWorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parts' => ['sometimes', 'array'],
            'parts.*.spare_part_id' => ['required', 'integer', 'exists:spare_parts,id'],
            'parts.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}