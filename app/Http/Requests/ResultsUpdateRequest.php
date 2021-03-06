<?php

namespace Bets\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResultsUpdateRequest extends FormRequest
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
            'home_1st' => 'required|min:0',
            'away_1st' => 'required|min:0',
            'home_2nd' => 'required|min:0',
            'away_2nd' => 'required|min:0',
            'home_final' => 'required|min:0',
            'away_final' => 'required|min:0',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Este campo é obrigatório!'
        ];
    }
}
