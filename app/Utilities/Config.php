<?php

namespace App\Utilities;

class Config
{
    public static function timestampFromFlowToDate($timestamp): string
    {
        return date("Y-m-d H:i:s", $timestamp);
    }
}
