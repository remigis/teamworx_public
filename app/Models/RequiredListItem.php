<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\RequiredListItem
 *
 * @property int $id
 * @property int $required_list_id
 * @property string $rz
 * @property string $name
 * @property int|null $count
 * @property int|null $required
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\RequiredList|null $requiredList
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListItem whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListItem whereRequiredListId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListItem whereRz($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredListItem whereUpdatedAt($value)
 * @mixin Eloquent
 */
class RequiredListItem extends Model
{
    use HasFactory;

    public function requiredList()
    {
        return $this->belongsTo(RequiredList::class);
    }

}
