<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SnLookOverRules
 *
 * @property int $id
 * @property string $regex
 * @property int $sn_look_over_group_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SnLookOverRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SnLookOverRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SnLookOverRule query()
 * @method static \Illuminate\Database\Eloquent\Builder|SnLookOverRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SnLookOverRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SnLookOverRule whereRegex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SnLookOverRule whereSnLookOverGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SnLookOverRule whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\SnLookOverGroup|null $group
 * @property string $rule
 * @method static \Illuminate\Database\Eloquent\Builder|SnLookOverRule whereRule($value)
 * @property-read \App\Models\SnLookOverGroup|null $snLookOverGroup
 */
class SnLookOverRule extends Model
{
    protected $fillable = ['sn_look_over_group_id', 'rule', 'regex'];

    use HasFactory;

    public function snLookOverGroup()
    {
        return $this->belongsTo(SnLookOverGroup::class);
    }
}
