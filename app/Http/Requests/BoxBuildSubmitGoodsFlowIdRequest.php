<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $goodsFlow
 */
class BoxBuildSubmitGoodsFlowIdRequest extends FormRequest
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
            'goodsFlow' => 'required|exists:mysql2.flow_karton_artikel,gUID|unique:mysql.box_build_box_items,gf'
        ];
    }
}
