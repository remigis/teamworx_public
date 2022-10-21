<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 *
 * @property mixed $name
 * @property mixed $pitch
 */
class ChangeUsersVoiceSettingsRequest extends FormRequest
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
            'name' => 'required|exists:mysql.voices,name',
            'pitch' => 'required|min:-100|max:100',
        ];
    }
}
