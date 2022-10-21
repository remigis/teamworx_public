<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property $id
 * @property $name
 * @property $audio_text
 */
class EditWarehouseCenterRequest extends FormRequest
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
            'id' => 'required|exists:mysql.warehouse_centers,id',
            'name' => [
                'required',
                'string',
                Rule::unique('mysql.warehouse_centers', 'name')->ignore($this->id),
            ],
            'box_prefix' => [
                'required',
                'string',
                'size:2',
                Rule::unique('mysql.warehouse_centers', 'name')->ignore($this->id),
            ],
            'audio_text' => [
                'required',
                'string',
                Rule::unique('mysql.warehouse_centers', 'audio_text')->ignore($this->id),
            ]
        ];
    }
}
