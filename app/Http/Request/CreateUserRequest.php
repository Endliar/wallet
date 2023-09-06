<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize() : bool
    {
        return true;
    }

    public function rules(): array
    {
        return
        [
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required']
        ];
    }

    public function messages(): array
    {
        return
        [
            'name.required' => 'Укажите имя',
            'email.required' => 'Укажите email',
            'password.required' => 'Укажите пароль'
        ];
    }
}
