<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // 'closed' hanya lewat endpoint /complete agar stok ikut terpotong
        return [
            'status' => ['required', Rule::in(['open', 'in_progress', 'pending_parts'])],
        ];
    }
}