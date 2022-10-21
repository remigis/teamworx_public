<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 *
 * @property mixed $boxId
 * @property mixed $count
 */
class BoxLabelRequest extends FormRequest
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
            'boxId' => 'required|exists:mysql2.flow_karton,id',
            'count' => 'required|regex:/^([0-9]+)$/'
        ];
    }

    public function all($keys = null): array
    {
        $data = parent::all();
        $data['boxId'] = $this->route('boxId');
        $data['count'] = $this->route('count');

        return $data;
    }
}
