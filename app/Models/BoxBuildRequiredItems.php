<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BoxBuildRequiredItems
 *
 * @property int $id
 * @property int $box_build_id
 * @property string $product_condition
 * @property string $warehouse_center_id
 * @property string $vid
 * @property string $manufacturer
 * @property int $required
 * @property int $collected
 * @property int $need
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildRequiredItems newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildRequiredItems newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildRequiredItems query()
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildRequiredItems whereBoxBuildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildRequiredItems whereCollected($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildRequiredItems whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildRequiredItems whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildRequiredItems whereManufacturer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildRequiredItems whereNeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildRequiredItems whereProductCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildRequiredItems whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildRequiredItems whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildRequiredItems whereVid($value)
 * @mixin \Eloquent
 * @property-read \App\Models\BoxBuild|null $boxBuild
 * @property-read \App\Models\WarehouseCenter|null $warehouseCenter
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildRequiredItems whereWarehouseCenterId($value)
 * @property int $priority
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildRequiredItems wherePriority($value)
 */
class BoxBuildRequiredItems extends Model
{
    use HasFactory;

    public function boxBuild()
    {
        return $this->belongsTo(BoxBuild::class);
    }

    public function warehouseCenter()
    {
        return $this->belongsTo(WarehouseCenter::class);
    }
}
