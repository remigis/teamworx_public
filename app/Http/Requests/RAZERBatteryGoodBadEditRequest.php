<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property $rz
 * @property $scrap
 * @property $battery
 * @property $ean
 */
class RAZERBatteryGoodBadEditRequest extends FormRequest
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
            'rz' => 'required|exists:mysql.razer_battery_good_bads,rz',
            'ean' => Rule::unique('razer_battery_good_bads', 'ean')->where(function ($query) {
                $query->where('rz', '!=', $this->rz);
            }),
            'scrap' => 'required|in:Yes,No',
            'battery' => 'required|in:Yes,No',
        ];
    }
}
