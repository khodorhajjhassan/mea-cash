<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $userId],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => [$this->isMethod('POST') ? 'required' : 'nullable', 'string', 'min:8', 'confirmed'],
            'is_admin' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'preferred_language' => ['sometimes', 'in:en,ar'],
        ];
    }
}
