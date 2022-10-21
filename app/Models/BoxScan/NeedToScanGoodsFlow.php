<?php

namespace App\Models\BoxScan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\BoxScan\NeedToScanGoodsFlow
 *
 * @property int $id
 * @property int $box_id
 * @property int $scan_id
 * @property string $tester
 * @property string $zustand
 * @property string $seriennummer
 * @property string $kommentar
 * @property string $time
 * @property string $goodsflow
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|NeedToScanGoodsFlow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NeedToScanGoodsFlow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NeedToScanGoodsFlow query()
 * @method static \Illuminate\Database\Eloquent\Builder|NeedToScanGoodsFlow whereBoxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeedToScanGoodsFlow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeedToScanGoodsFlow whereGoodsflow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeedToScanGoodsFlow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeedToScanGoodsFlow whereKommentar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeedToScanGoodsFlow whereScanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeedToScanGoodsFlow whereSeriennummer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeedToScanGoodsFlow whereTester($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeedToScanGoodsFlow whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeedToScanGoodsFlow whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeedToScanGoodsFlow whereZustand($value)
 * @mixin \Eloquent
 */
class NeedToScanGoodsFlow extends Model
{
    use HasFactory;
}
