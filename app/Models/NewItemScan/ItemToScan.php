<?php

namespace App\Models\NewItemScan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\NewItemScan\ItemToScan
 *
 * @property int $id
 * @property int $new_item_scan_id
 * @property string $product
 * @property string $product_description
 * @property string $serial_number
 * @property string $defect
 * @property string|null $date_from_distributor
 * @property string|null $date_from_customer
 * @property string|null $remarks
 * @property string $product_checked
 * @property string $description
 * @property string $remarks_product_code
 * @property string $remarks_defect_description
 * @property string $remarks_sn
 * @property string $status
 * @property string $qty
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan query()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan whereDateFromCustomer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan whereDateFromDistributor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan whereDefect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan whereNewItemScanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan whereProductChecked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan whereProductDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan whereRemarksDefectDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan whereRemarksProductCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan whereRemarksSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemToScan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ItemToScan extends Model
{
    use HasFactory;
}
