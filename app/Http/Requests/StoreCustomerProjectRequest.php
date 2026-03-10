<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'name' => ['required', 'string', 'max:150'],
            'project_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
        ];
    }
}