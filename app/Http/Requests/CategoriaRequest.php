<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class CategoriaRequest extends FormRequest
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
            'code' => 'required|string|max:15',
            'descripcion' => 'required|string|max:60'
        ];
    }

    public function attributes()
    {
        return [
            'code' => 'Código de la categoría',
            'descripcion' => 'Nombre de la categoría'
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Debe completar los campos en :attribute',
            'string' => 'Debe ser una cadena de texto',
            'max' => ':attribute acepta un máximo de :max caracteres',
        ];
    }

    public function formatErrors(Validator $validator)
    {
        return [ 'error' => $validator->errors()->first() ];
    }
}
