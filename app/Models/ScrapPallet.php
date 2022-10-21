<?php

namespace App\Models;

use App\Models\NewItemScan\ScrapItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\ScrapPallet
 *
 * @property int $id
 * @property string $name
 * @property string $text
 * @property string $type
 * @property int|null $battery
 * @property int $exported
 * @property int $closed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|ScrapItem[] $scrapItems
 * @property-read int|null $scrap_items_count
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapPallet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapPallet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapPallet query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapPallet whereBattery($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapPallet whereClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapPallet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapPallet whereExported($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapPallet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapPallet whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapPallet whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapPallet whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapPallet whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ScrapPallet extends Model
{
    use HasFactory;

    public function scrapItems()
    {
        return $this->hasMany(ScrapItem::class);
    }
}
