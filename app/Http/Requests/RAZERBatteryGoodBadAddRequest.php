<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $rz
 * @property $scrap
 * @property $battery
 * @property $ean
 */
class RAZERBatteryGoodBadAddRequest extends FormRequest
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
            'rz' => 'required|unique:mysql.razer_battery_good_bads,rz',
            'ean' => 'required|unique:mysql.razer_battery_good_bads,ean',
            'scrap' => 'required|in:Yes,No',
            'battery' => 'required|in:Yes,No',

        ];
    }

}
