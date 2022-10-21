<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BoxBuild
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuild newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuild newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuild query()
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuild whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuild whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuild whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuild whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $active
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BoxBuildRequiredItems[] $boxBuildRequiredItems
 * @property-read int|null $box_build_required_items_count
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuild whereActive($value)
 */
class BoxBuild extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function boxBuildRequiredItems()
    {
        return $this->hasMany(BoxBuildRequiredItems::class);
    }
}
