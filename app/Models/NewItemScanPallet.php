<?php

namespace App\Models;

use App\Models\NewItemScan\NewItemScan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\NewItemScanPallet
 *
 * @property int $id
 * @property string $name
 * @property int $new_item_scan_id
 * @property int $pallet_number
 * @property string $text
 * @property int $closed
 * @property int $box_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read NewItemScan|null $newItemScan
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NewItemScanPalletItem[] $newItemScanPalletItems
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPallet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPallet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPallet query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPallet whereClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPallet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPallet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPallet whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPallet whereNewItemScanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPallet wherePalletNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPallet whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPallet whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read int|null $new_item_scan_pallet_items_count
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPallet whereBoxNumber($value)
 */
class NewItemScanPallet extends Model
{
    use HasFactory;

    protected $casts = ['closed' => 'boolean'];

    public function newItemScanPalletItems()
    {
        return $this->hasMany(NewItemScanPalletItem::class);
    }

    public function newItemScan()
    {
        return $this->belongsTo(NewItemScan::class);
    }
}
