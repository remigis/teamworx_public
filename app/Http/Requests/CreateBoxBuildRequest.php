<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 *
 * @property $file
 * @property $vid_column
 * @property $product_condition_column
 * @property $sheet_name
 * @property $manufacturer
 * @property $fulfilment_centers
 */
class CreateBoxBuildRequest extends FormRequest
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
            'file' => 'required|mimes:xlsx',
            'vid_column' => 'required|string',
            'product_condition_column' => 'required|string',
            'sheet_name' => 'required|string',
            'manufacturer' => 'required|string',
            'fulfilment_centers' => 'required|array|min:1',
            'fulfilment_centers.*.column' => 'required|string',
            'fulfilment_centers.*.id' => 'required|exists:mysql.warehouse_centers,id',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('fulfilment_centers')) {
            $ffCentersInArray = [];

            foreach ($this->fulfilment_centers as $fulfilment_center) {
                $ffCentersInArray[] = json_decode($fulfilment_center, true);
            }

            foreach ($ffCentersInArray as $key => $ffCenter) {
                $ffCentersInArray[$key]['column'] = strtoupper($ffCenter['column']);
            }

            $this->merge(['fulfilment_centers' => $ffCentersInArray]);
        }


        if ($this->has('vid_column')) {
            $this->merge(['vid_column' => strtoupper($this->vid_column)]);
        }

        if ($this->has('product_condition_column')) {
            $this->merge(['product_condition_column' => strtoupper($this->product_condition_column)]);
        }

        if ($this->has('manufacturer')) {
            $this->merge(['manufacturer' => strtoupper($this->manufacturer)]);
        }

    }
}
