<?php

namespace App\Models;

use App\Models\NewItemScan\NewItemScan;
use App\Models\NewItemScan\ScannedSn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\NewItemScanPalletItem
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\NewItemScanPallet|null $newItemScanPallet
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPalletItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPalletItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPalletItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPalletItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPalletItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPalletItem whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $sn
 * @property string $rz
 * @property int $user_id
 * @property int $new_item_scan_id
 * @property int $new_item_scan_pallet_id
 * @property int $scanned_sn_id
 * @property-read NewItemScan|null $newItemScan
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPalletItem whereNewItemScanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPalletItem whereNewItemScanPalletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPalletItem whereRz($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPalletItem whereSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPalletItem whereUserId($value)
 * @property-read ScannedSn|null $scannedSn
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScanPalletItem whereScannedSnId($value)
 */
class NewItemScanPalletItem extends Model
{
    use HasFactory;

    public function newItemScanPallet()
    {
        return $this->belongsTo(NewItemScanPallet::class);
    }

    public function newItemScan()
    {
        return $this->belongsTo(NewItemScan::class);
    }

    public function scannedSn()
    {
        return $this->belongsTo(ScannedSn::class);
    }

}
