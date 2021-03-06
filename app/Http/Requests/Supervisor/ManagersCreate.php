<?php

namespace Bets\Http\Requests\Supervisor;

use Illuminate\Foundation\Http\FormRequest;

class ManagersCreate extends FormRequest
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
            'company_id' => 'required',
            'balance' => 'required',
            'tips_min' => 'required',
            'tips_max' => 'required',
            'value_min1' => 'required',
            'value_max1' => 'required',
            'commission1' => 'required',
            'value_min2' => 'required',
            'value_max2' => 'required',
            'commission2' => 'required',
            'value_min3' => 'required',
            'value_max3' => 'required',
            'commission3' => 'required',
            'profit_percentage' => 'required',
            'manager_commission' => 'required',
            'max_prize' => 'required',
            'max_prize_multiplier' => 'required',
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
