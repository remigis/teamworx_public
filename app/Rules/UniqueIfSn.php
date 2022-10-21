<?php

namespace App\Rules;

use App\Models\NewItemScan\ScannedSn;
use App\Utilities\Constants;
use Illuminate\Contracts\Validation\Rule;

class UniqueIfSn implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($value === Constants::NO_SN_VALUE) {
            return true;
        }

        if (ScannedSn::whereSn($value)->exists()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Serial Number should be unique.';
    }
}
