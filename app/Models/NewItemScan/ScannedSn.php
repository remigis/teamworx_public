<?php

namespace App\Models\NewItemScan;

use App\Models\NewItemScanPalletItem;
use App\Models\RequiredListPalletItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * App\Models\NewItemScan\ScannedSn
 *
 * @property int $id
 * @property string $sn
 * @property string $rz
 * @property int $user_id
 * @property int $new_item_scan_id
 * @property int $direct_scarp
 * @property int $direct_scarp_result
 * @property int $check_required
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read NewItemScan|null $newItemScan
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedSn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedSn newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedSn query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedSn whereCheckRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedSn whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedSn whereDirectScarp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedSn whereDirectScarpResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedSn whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedSn whereNewItemScanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedSn whereRz($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedSn whereSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedSn whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedSn whereUserId($value)
 * @mixin \Eloquent
 * @property string|null $result
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedSn whereResult($value)
 * @property-read NewItemScanPalletItem|null $regularItem
 * @property-read RequiredListPalletItem|null $requiredItem
 * @property-read \App\Models\NewItemScan\ScrapItem|null $scrapItem
 */
class ScannedSn extends Model
{
    use HasFactory;

    protected $fillable = ['result'];

    public function newItemScan(): BelongsTo
    {
        return $this->belongsTo(NewItemScan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scrapItem()
    {
        return $this->hasOne(ScrapItem::class);
    }

    public function requiredItem()
    {
        return $this->hasOne(RequiredListPalletItem::class);
    }

    public function regularItem()
    {
        return $this->hasOne(NewItemScanPalletItem::class);
    }
}
