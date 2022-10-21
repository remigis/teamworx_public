<?php

namespace App\Models\Flow;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Flow\Liferant
 *
 * @property int $id
 * @property string|null $name Firmenname
 * @property int $ignorefirstline
 * @property int $metadata_id
 * @property string $shop_id
 * @property string|null $gdrive_folder
 * @property string|null $gdrive_folder_supplier
 * @property int|null $buy_all
 * @property int $profit_sharing
 * @property string $shortcode Wird verwendet um die Statistiken bei Lieferanten mit Subherstellern zusammenzufassen
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Flow\KartonArtikel[] $kartonArtikels
 * @property-read int|null $karton_artikels_count
 * @method static \Illuminate\Database\Eloquent\Builder|Liferant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Liferant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Liferant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Liferant whereBuyAll($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liferant whereGdriveFolder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liferant whereGdriveFolderSupplier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liferant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liferant whereIgnorefirstline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liferant whereMetadataId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liferant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liferant whereProfitSharing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liferant whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liferant whereShortcode($value)
 * @mixin \Eloquent
 */
class Liferant extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';

    protected $table = 'flow_lieferant';

    public function kartonArtikels()
    {
        return $this->HasMany(KartonArtikel::class, 'lieferant_id', 'id');
    }

}
