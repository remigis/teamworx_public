<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\RazerBatteryGoodBad
 *
 * @property int $id
 * @property int|null $scrap
 * @property int|null $battery
 * @property string $rz
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|RazerBatteryGoodBad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RazerBatteryGoodBad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RazerBatteryGoodBad query()
 * @method static \Illuminate\Database\Eloquent\Builder|RazerBatteryGoodBad whereBattery($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazerBatteryGoodBad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazerBatteryGoodBad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazerBatteryGoodBad whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazerBatteryGoodBad whereRz($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazerBatteryGoodBad whereScrap($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazerBatteryGoodBad whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $ean
 * @method static \Illuminate\Database\Eloquent\Builder|RazerBatteryGoodBad whereEan($value)
 */
class RazerBatteryGoodBad extends Model
{
    use HasFactory;
}
