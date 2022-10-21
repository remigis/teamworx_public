<?php

namespace App\Models\NewItemScan;

use App\Models\ScrapPallet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\NewItemScan\ScrapItem
 *
 * @property int $id
 * @property string $sn
 * @property string $rz
 * @property int $user_id
 * @property int $new_item_scan_id
 * @property int $scrap_pallet_id
 * @property int $scanned_sn_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read NewItemScan|null $newItemScan
 * @property-read ScrapPallet|null $scrapPallet
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapItem whereNewItemScanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapItem whereRz($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapItem whereScrapPalletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapItem whereSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapItem whereUserId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\NewItemScan\ScannedSn|null $scannedSn
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapItem whereScannedSnId($value)
 */
class ScrapItem extends Model
{
    use HasFactory;

    public function newItemScan()
    {
        return $this->belongsTo(NewItemScan::class);
    }

    public function scrapPallet()
    {
        return $this->belongsTo(ScrapPallet::class);
    }

    public function scannedSn()
    {
        return $this->belongsTo(ScannedSn::class);
    }
}
