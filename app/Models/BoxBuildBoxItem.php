<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BoxBuildBoxItem
 *
 * @property-read \App\Models\BoxBuildBox|null $box
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBoxItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBoxItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBoxItem query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $box_build_box_id
 * @property string $product
 * @property string $condition
 * @property string $gf
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBoxItem whereBoxBuildBoxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBoxItem whereCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBoxItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBoxItem whereGf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBoxItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBoxItem whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBoxItem whereUpdatedAt($value)
 * @property-read \App\Models\BoxBuildBox|null $boxBuildBox
 * @property string|null $vid
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBoxItem whereVid($value)
 * @property boolean $condition_not_important
 * @method static \Illuminate\Database\Eloquent\Builder|BoxBuildBoxItem whereConditionNotImportant($value)
 */
class BoxBuildBoxItem extends Model
{
    protected $casts = ['condition_not_important' => 'boolean'];
    protected $fillable = ['product', 'condition', 'gf', 'box_build_box_id', 'vid', 'condition_not_important'];

    use HasFactory;

    public function boxBuildBox()
    {
        return $this->belongsTo(BoxBuildBox::class);
    }
}
