<?php

namespace Bets\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BetsStoreRequest extends FormRequest
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
            'bet_value' => 'required|numeric',
            'name' => 'required',
            'choices' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'choices.required'  => 'Escolha ao menos um palpite',
            'name.required'  => 'O nome é obrigatório',
            'bet_value.required' => 'O valor é obrigatório',
        ];
    }
}
