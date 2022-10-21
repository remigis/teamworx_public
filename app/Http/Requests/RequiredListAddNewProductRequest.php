<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property $listId
 * @property $rz
 * @property $count
 */
class RequiredListAddNewProductRequest extends FormRequest
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
            'listId' => 'required|exists:mysql.required_lists,id',
            'rz' => ['required', 'exists:mysql.razer_battery_good_bads,rz', Rule::unique('required_list_items', 'rz')->where('required_list_id', $this->listId)],
            'count' => 'required|numeric',
        ];
    }
}
