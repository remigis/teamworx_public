<?php

namespace App\Utilities;

use Illuminate\Support\Str;

class WarehouseLabel
{
    public static function print($warehouseLabelKey)
    {
        $label = \App\Models\WarehouseLabel::whereWarehouseKey($warehouseLabelKey)->first();

        $data['text'] = $label->text;
        $data['number'] = $label->number;
        $data['key'] = $label->warehouse_key;

        return $data;
    }

    /**
     * Returns WarehouseLabel Object on success and false on failure
     *
     * @param $text
     * @param $number
     * @param $pallet_type_id
     * @param $warehouse_position_id
     * @return \App\Models\WarehouseLabel|false|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public function generate($text, $number = null, $pallet_type_id = null, $warehouse_position_id = null)
    {
        $label = \App\Models\WarehouseLabel::whereNumber($number)->whereText($text)->first();
        if ($label) {
            return $label;
        }

        $label = new \App\Models\WarehouseLabel();
        $label->warehouse_key = $this->keyGenerate();
        $label->text = $text;
        $label->number = $number;
        $label->warehouse_pallet_type_id = $pallet_type_id;
        $label->warehouse_position_id = $warehouse_position_id;
        if ($label->save()) {
            return $label;
        } else {
            return false;
        }

    }


    private function keyGenerate(): string
    {
        $key = Str::random();

        if (\App\Models\WarehouseLabel::whereWarehouseKey($key)->exists()) {
            return $this->keyGenerate();
        }

        return $key;
    }
}
