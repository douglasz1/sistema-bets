<?php

namespace Bets\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SupervisorsCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:3',
            'username' => 'required|unique:users|min:3',
            'password' => 'required|confirmed|min:3',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Este campo é obrigatório!',
            'username.min' => 'O nome de usuário deve conter, no mínimo, 3 caracteres.',
            'username.unique' => 'Este nome de usuário já está em uso.',
        ];
    }
}
