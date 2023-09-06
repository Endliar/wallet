<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class AuthUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return
        [
            'email' => ['required', 'email'],
            'password' => ['required']
        ];
    }

    public function messages(): array
    {
        return
        [
            'email.required' => 'Укажите email',
            'password.required' => 'Укажите пароль'
        ];
    }
}
