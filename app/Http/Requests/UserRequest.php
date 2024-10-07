<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'timezone' => 'string|max:255',
        ];

        if ($this->isMethod('put')) {
            $rules['email'] = 'required|string|email|max:255|unique:users,email,' . $this->route('id');
        }

        return $rules;
    }
}