<?php

namespace App\Models\flow;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\flow\Sphere
 *
 * @property int $id
 * @property string|null $name Sphärenname
 * @property string|null $color Sphärenfarbe
 * @property int $metadata_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Flow\KartonArtikel[] $kartonArtikels
 * @property-read int|null $karton_artikels_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Flow\Karton[] $kartons
 * @property-read int|null $kartons_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Flow\Palette[] $palettes
 * @property-read int|null $palettes_count
 * @method static \Illuminate\Database\Eloquent\Builder|Sphere newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sphere newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sphere query()
 * @method static \Illuminate\Database\Eloquent\Builder|Sphere whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sphere whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sphere whereMetadataId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sphere whereName($value)
 * @mixin \Eloquent
 */
class Sphere extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';

    protected $table = 'flow_sphere';

    public function kartonArtikels()
    {
        return $this->hasMany(KartonArtikel::class, 'id', 'sphere_id');
    }

    public function kartons()
    {
        return $this->hasMany(Karton::class, 'id', 'sphere_id');
    }

    public function palettes()
    {
        return $this->hasMany(Palette::class, 'id', 'sphere_id');
    }
}
