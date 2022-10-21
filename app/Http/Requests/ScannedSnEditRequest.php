<?php

namespace App\Http\Requests;

use App\Rules\UniqueIfSn;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $sn
 * @property $id
 */
class ScannedSnEditRequest extends FormRequest
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
            'sn' => ['required', new UniqueIfSn()],
            'id' => 'required|exists:mysql.scanned_sns,id',
        ];
    }
}
