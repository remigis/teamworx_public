<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $putToDirectScrap
 * @property $sn
 * @property $rz
 * @property $snCount
 * @property $scanId
 */
class MultipleNoSnSubmitRequest extends FormRequest
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
            'putToDirectScrap' => 'required|boolean',
            'sn' => 'required',
            'rz' => 'required|exists:mysql.razer_battery_good_bads,rz',
            'snCount' => 'required|numeric',
            'scanId' => 'exists:mysql.new_item_scans,id'
        ];
    }
}
