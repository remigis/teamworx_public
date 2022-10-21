<?php

namespace App\Http\Requests;

use App\Rules\UniqueIfSn;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $putToDirectScrap
 * @property $sn
 * @property $rz
 * @property $scanId
 */
class SNSubmitRequest extends FormRequest
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
            'sn' => ['required', new UniqueIfSn()],
            'rz' => 'nullable|exists:mysql.razer_battery_good_bads,rz',
            'scanId' => 'required|exists:mysql.new_item_scans,id'
        ];
    }
}
