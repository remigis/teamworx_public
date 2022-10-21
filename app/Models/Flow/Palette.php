<?php

namespace App\Models\Flow;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Flow\Palette
 *
 * @property int $id
 * @property string|null $name Palettenname
 * @property int $sphere_id Sphäre
 * @property int $metadata_id
 * @property int $flagged
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Flow\Karton[] $kartons
 * @property-read int|null $kartons_count
 * @property-read \App\Models\flow\Sphere $sphere
 * @method static \Illuminate\Database\Eloquent\Builder|Palette newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Palette newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Palette query()
 * @method static \Illuminate\Database\Eloquent\Builder|Palette whereFlagged($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Palette whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Palette whereMetadataId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Palette whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Palette whereSphereId($value)
 * @mixin \Eloquent
 */
class Palette extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';

    protected $table = 'flow_palette';

    public function kartons()
    {
        return $this->HasMany(Karton::class, 'palette_id', 'id');
    }

    public function sphere()
    {
        return $this->belongsTo(Sphere::class, 'sphere_id', 'id');
    }
}
