<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 *
 * @property mixed $id
 */
class BoxRequest extends FormRequest
{
    protected $redirectRoute = 'calculatorView';
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
            'id' => 'required|exists:mysql2.flow_karton,id'
        ];
    }

    public function all($keys = null): array
    {
        $data = parent::all();
        $data['id'] = $this->route('id');

        return $data;
    }
}
