<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BoxBuildBox
 *
 * @property int $id
 * @property string $name
 * @property int $warehouse_center_id
 * @property int $box_build_id
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBox newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBox newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBox query()
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBox whereBoxBuildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBox whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBox whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBox whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBox whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBox whereWarehouseCenterId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBox whereActive($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BoxBuildBoxItem[] $items
 * @property-read int|null $items_count
 * @property-read \App\Models\WarehouseCenter|null $warehouseCenter
 */
class BoxBuildBox extends Model
{
    protected $fillable = ['box_build_id', 'warehouse_center_id', 'active', 'name'];

    use HasFactory;

    public function items()
    {
        return $this->hasMany(BoxBuildBoxItem::class);
    }

    public function warehouseCenter()
    {
        return $this->belongsTo(WarehouseCenter::class);
    }
}
