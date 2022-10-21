<?php

namespace App\Models\BoxScan;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\BoxScan\ScannedGoodsFlowId
 *
 * @property int $id
 * @property int $user_id
 * @property int $box_id
 * @property int $scan_id
 * @property string $goods_flow_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedGoodsFlowId newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedGoodsFlowId newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedGoodsFlowId query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedGoodsFlowId whereBoxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedGoodsFlowId whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedGoodsFlowId whereGoodsFlowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedGoodsFlowId whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedGoodsFlowId whereScanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedGoodsFlowId whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScannedGoodsFlowId whereUserId($value)
 * @mixin \Eloquent
 */
class ScannedGoodsFlowId extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
