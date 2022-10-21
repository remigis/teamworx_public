<?php

namespace App\Models;

use App\Models\NewItemScan\NewItemScan;
use App\Models\NewItemScan\ScannedSn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\RequiredListPalletItem
 *
 * @property int $id
 * @property string $sn
 * @property string $rz
 * @property int $user_id
 * @property int $new_item_scan_id
 * @property int $required_list_pallet_id
 * @property int $required_list_id
 * @property int $scanned_sn_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read NewItemScan|null $newItemScan
 * @property-read \App\Models\RequiredList|null $requiredList
 * @property-read \App\Models\RequiredListPallet|null $requiredListPallet
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPalletItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPalletItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPalletItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPalletItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPalletItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPalletItem whereNewItemScanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPalletItem whereRequiredListId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPalletItem whereRequiredListPalletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPalletItem whereRz($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPalletItem whereSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPalletItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPalletItem whereUserId($value)
 * @mixin \Eloquent
 * @property-read ScannedSn|null $scannedSn
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPalletItem whereScannedSnId($value)
 */
class RequiredListPalletItem extends Model
{
    use HasFactory;

    public function requiredListPallet()
    {
        return $this->belongsTo(RequiredListPallet::class);
    }

    public function requiredList()
    {
        return $this->belongsTo(RequiredList::class);
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
