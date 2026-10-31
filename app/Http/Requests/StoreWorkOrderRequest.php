<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_id' => ['required', 'exists:assets,id'],
            'assigned_to' => ['nullable', Rule::exists('users', 'id')->where(
                fn ($q) => $q->whereIn('role_id', Role::where('name', 'Technician')->select('id'))
            )],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'critical'])],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }
}