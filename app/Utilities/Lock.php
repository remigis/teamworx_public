<?php

namespace App\Utilities;

use Illuminate\Support\Facades\DB;

class Lock
{
    public static function db(string|array $tableName)
    {
        $type = gettype($tableName);

        if ($type == 'string') {
            DB::raw('LOCK TABLES `' . $tableName . '` WRITE');
        }

        if ($type == 'array') {
            foreach ($tableName as $name) {
                DB::raw('LOCK TABLES `' . $name . '` WRITE');
            }
        }

    }

    public static function remove()
    {
        DB::raw('UNLOCK TABLES');
    }
}
