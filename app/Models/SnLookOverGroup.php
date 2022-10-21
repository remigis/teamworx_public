<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SnLookOverGroup
 *
 * @property int $id
 * @property string $name
 * @property int $active
 * @property int $active_for_auto_scan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SnLookOverGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SnLookOverGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SnLookOverGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|SnLookOverGroup whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SnLookOverGroup whereActiveForAutoScan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SnLookOverGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SnLookOverGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SnLookOverGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SnLookOverGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SnLookOverRule[] $rules
 * @property-read int|null $rules_count
 */
class SnLookOverGroup extends Model
{
    protected $fillable = ['name', 'active'];

    use HasFactory;

    public function rules()
    {
        return $this->hasMany(SnLookOverRule::class);
    }
}
