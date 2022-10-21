<?php

namespace App\Models\NewItemScan;

use App\Models\NewItemScanPallet;
use App\Models\NewItemScanPalletItem;
use App\Models\RequiredListPalletItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\NewItemScan\NewItemScan
 *
 * @property int $id
 * @property string $name
 * @property string $uploaded_file_path
 * @property string|null $export_file_path
 * @property string $ext
 * @property string $status
 * @property int $show
 * @property string $uploaded_file_name
 * @property string|null $export_file_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|NewItemScanPallet[] $newItemScanPallets
 * @property-read int|null $new_item_scan_pallets_count
 * @property-read \Illuminate\Database\Eloquent\Collection|RequiredListPalletItem[] $requiredListPalletItems
 * @property-read int|null $required_list_pallet_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NewItemScan\ScannedSn[] $scannedSns
 * @property-read int|null $scanned_sns_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NewItemScan\ScrapItem[] $scraps
 * @property-read int|null $scraps_count
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScan query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScan whereExportFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScan whereExportFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScan whereExt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScan whereShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScan whereUploadedFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScan whereUploadedFilePath($value)
 * @mixin \Eloquent
 * @property string $rma
 * @property string $sender
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScan whereRma($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewItemScan whereSender($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|NewItemScanPalletItem[] $newItemScanPalletItems
 * @property-read int|null $new_item_scan_pallet_items_count
 */
class NewItemScan extends Model
{
    use HasFactory;

    protected $fillable = ['status'];

    public function scannedSns()
    {
        return $this->hasMany(ScannedSn::class);
    }

    public function scraps()
    {
        return $this->hasMany(ScrapItem::class);
    }

    public function requiredListPalletItems()
    {
        return $this->hasMany(RequiredListPalletItem::class);
    }

    public function newItemScanPallets()
    {
        return $this->hasMany(NewItemScanPallet::class);
    }

    public function newItemScanPalletItems()
    {
        return $this->hasMany(NewItemScanPalletItem::class);
    }
}
