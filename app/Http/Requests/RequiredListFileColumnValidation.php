<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $required_list_id
 * @property $rz
 * @property $count
 * @property $name
 */
class RequiredListFileColumnValidation extends FormRequest
{
    private string $name;
    private int $required_list_id;
    private int $count;
    private string $rz;

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
            "required_list_id" => 'required|exists:mysql.required_lists,id',
            "rz" => "required|exists:mysql.razer_battery_good_bads,rz",
            "count" => "required|numeric",
            "name" => "required",
        ];
    }

    public function all($keys = null): array
    {
        $data = parent::all();

        $data['name'] = $this->name;
        $data['required_list_id'] = $this->required_list_id;
        $data['count'] = $this->count;
        $data['rz'] = $this->rz;


        return $data;
    }

    public function setValues($row)
    {
        $this->name = $row['name'];
        $this->required_list_id = $row['required_list_id'];
        $this->count = $row['count'];
        $this->rz = $row['rz'];
    }

    public function messages()
    {
        return [
            'rz.exists' => 'One or more products are not in the Battery/Scrap list',
        ];
    }
}
