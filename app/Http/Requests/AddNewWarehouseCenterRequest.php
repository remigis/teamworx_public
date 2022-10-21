<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $name
 * @property $box_prefix
 * @property $audio_text
 * @property $pallet_id
 */
class AddNewWarehouseCenterRequest extends FormRequest
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
            'name' => 'required|string|unique:mysql.warehouse_centers,name',
            'box_prefix' => 'required|string|unique:mysql.warehouse_centers,box_prefix|size:2',
            'audio_text' => 'required|string|unique:mysql.warehouse_centers,audio_text',
            'pallet_id' => 'required|exists:mysql2.flow_palette,id',
        ];
    }
}
