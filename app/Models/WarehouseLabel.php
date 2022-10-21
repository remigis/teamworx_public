<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\WarehouseLabel
 *
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseLabel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseLabel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseLabel query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $warehouse_key
 * @property string $text
 * @property string|null $number
 * @property string|null $warehouse_pallet_type_id
 * @property string|null $warehouse_position_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseLabel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseLabel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseLabel whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseLabel whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseLabel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseLabel whereWarehouseKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseLabel whereWarehousePalletTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseLabel whereWarehousePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseLabel whereWarehousePositionId($value)
 */
class WarehouseLabel extends Model
{
    use HasFactory;
}
