<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property $boxBuildId
 * @property $item
 */
class DeleteItemFromBoxBuildListRequest extends FormRequest
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
            'boxBuildId' => 'required|exists:mysql.box_builds,id',
            'item' => ['required', Rule::exists('mysql.box_build_required_items', 'product_condition')->where(function ($query) {
                return $query->where('box_build_id', $this->request->get('boxBuildId'));
            }),]
        ];
    }
}
