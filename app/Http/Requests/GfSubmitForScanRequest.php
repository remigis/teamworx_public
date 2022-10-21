<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $gf
 * @property $boxId
 * @property $scanId
 */
class GfSubmitForScanRequest extends FormRequest
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
            'gf' => 'required',
            'boxId' => 'required|exists:mysql2.flow_karton,id',
            'scanId' => 'required',
        ];
    }
}
