<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_code' => [
                'sometimes', 'string', 'max:50',
                Rule::unique('assets', 'asset_code')->ignore($this->route('asset')),
            ],
            'name' => ['sometimes', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'status' => ['sometimes', Rule::in(['active', 'under_maintenance', 'broken'])],
        ];
    }
}