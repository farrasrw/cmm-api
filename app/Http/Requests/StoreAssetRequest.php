<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // pembatasan akses ditangani middleware role
    }

    public function rules(): array
    {
        return [
            'asset_code' => ['required', 'string', 'max:50', 'unique:assets,asset_code'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'status' => ['sometimes', Rule::in(['active', 'under_maintenance', 'broken'])],
        ];
    }
}