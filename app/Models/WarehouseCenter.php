<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\WarehouseCenter
 *
 * @property int $id
 * @property string $name
 * @property string $audio_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseCenter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseCenter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseCenter query()
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseCenter whereAudioText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseCenter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseCenter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseCenter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseCenter whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Query\Builder|WarehouseCenter onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseCenter whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|WarehouseCenter withTrashed()
 * @method static \Illuminate\Database\Query\Builder|WarehouseCenter withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BoxBuildRequiredItems[] $boxBuildRequiredItems
 * @property-read int|null $box_build_required_items_count
 * @property string $box_prefix
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseCenter whereBoxPrefix($value)
 * @property string $pallet_id
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseCenter wherePalletId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BoxBuildBox[] $boxBuildBoxes
 * @property-read int|null $box_build_boxes_count
 */
class WarehouseCenter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'audio_text', 'box_prefix', 'pallet_id'];

    public function boxBuildRequiredItems()
    {
        return $this->hasMany(BoxBuildRequiredItems::class);
    }

    public function boxBuildBoxes()
    {
        return $this->hasMany(BoxBuildBox::class);
    }
}
