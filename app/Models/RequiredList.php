<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\RequiredList
 *
 * @property int $id
 * @property string $name
 * @property string $audioText
 * @property int $active
 * @property int|null $priority
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RequiredListItem[] $requiredListItems
 * @property-read int|null $required_list_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RequiredListPalletItem[] $requiredListPalletItems
 * @property-read int|null $required_list_pallet_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RequiredListPallet[] $requiredListPallets
 * @property-read int|null $required_list_pallets_count
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredList query()
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredList whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredList whereAudioText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredList whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredList wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredList whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RequiredList extends Model
{
    use HasFactory;

    public function requiredListItems()
    {
        return $this->hasMany(RequiredListItem::class);
    }

    public function requiredListPalletItems()
    {
        return $this->hasMany(RequiredListPalletItem::class);
    }

    public function requiredListPallets()
    {
        return $this->hasMany(RequiredListPallet::class);
    }

}
