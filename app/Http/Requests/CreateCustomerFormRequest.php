<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCustomerFormRequest extends FormRequest
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
            'name' => 'required|max:80',
            'last_name' => 'required|max:100',
            'email' => 'required|email',
            'ci' => 'nullable|ecuador:ci',
            'avatar' => 'nullable|image|max:1024',
            'phone' => 'required|max:10',
            'cell_phone' => 'required|max:10',
            'address' => 'required|max:255',
            'birthdate' => 'required|date',
            'gender' => 'required|max:60',
            'routine_id' => 'exists:routines,id',
            'level_id' => 'exists:levels,id',
        ];
    }
}
