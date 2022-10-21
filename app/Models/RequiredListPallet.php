<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\RequiredListPallet
 *
 * @property int $id
 * @property string $name
 * @property int $required_list_id
 * @property string $text
 * @property int $closed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\RequiredList|null $requiredList
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RequiredListPalletItem[] $requiredListPalletItems
 * @property-read int|null $required_list_pallet_items_count
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPallet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPallet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPallet query()
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPallet whereClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPallet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPallet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPallet whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPallet whereRequiredListId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPallet whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListPallet whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RequiredListPallet extends Model
{
    use HasFactory;

    protected $fillable = ['closed'];

    public function requiredListPalletItems()
    {
        return $this->hasMany(RequiredListPalletItem::class);
    }

    public function requiredList()
    {
        return $this->belongsTo(RequiredList::class);
    }
}
