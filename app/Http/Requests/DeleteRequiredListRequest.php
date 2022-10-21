<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 *
 * @property mixed $listId
 *
 */
class DeleteRequiredListRequest extends FormRequest
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
            'listId' => 'required|exists:mysql.required_lists,id'
        ];
    }
}
