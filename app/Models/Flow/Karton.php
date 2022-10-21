<?php

namespace App\Models\Flow;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Flow\Karton
 *
 * @property int $id
 * @property string|null $name Kartonname
 * @property int $sphere_id Sphäre
 * @property int|null $palette_id Palette
 * @property int|null $status Kartonstatus
 * @property string|null $kommentar Kartonkommentar
 * @property int $metadata_id
 * @property int $flagged
 * @property int $locked
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Flow\KartonArtikel[] $kartonArtikels
 * @property-read int|null $karton_artikels_count
 * @property-read \App\Models\Flow\Palette|null $palette
 * @property-read \App\Models\flow\Sphere $sphere
 * @method static \Illuminate\Database\Eloquent\Builder|Karton newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Karton newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Karton query()
 * @method static \Illuminate\Database\Eloquent\Builder|Karton whereFlagged($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Karton whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Karton whereKommentar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Karton whereLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Karton whereMetadataId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Karton whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Karton wherePaletteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Karton whereSphereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Karton whereStatus($value)
 * @mixin \Eloquent
 */
class Karton extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';

    protected $table = 'flow_karton';

    public function kartonArtikels()
    {
        return $this->HasMany(KartonArtikel::class, 'karton_id', 'id');
    }

    public function sphere()
    {
        return $this->belongsTo(Sphere::class, 'sphere_id', 'id');
    }

    public function palette()
    {
        return $this->belongsTo(Palette::class, 'palette_id', 'id');
    }
}
