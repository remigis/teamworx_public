<?php

namespace App\Models\Flow;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Post
 *
 * @mixin Builder
 * @property int $id
 * @property int $artikel_id
 * @property int|null $karton_id
 * @property int|null $karton_delivered_id
 * @property string|null $seriennummer Seriennummer
 * @property string $import_serial Importierte Seriennummer
 * @property string|null $zustand Zustand
 * @property string|null $gUID Goodsflow TrackrID
 * @property float|null $einkaufspreis Einkaufspreis
 * @property float $einkaufspreis_rated
 * @property int $verloren Artikel Verloren
 * @property int $ueberschuss Überschuss
 * @property string|null $status Artikelstatus
 * @property int $lieferant_id Artikellieferant
 * @property int|null $lieferant_sortiment_id
 * @property string $lieferschein
 * @property int $metadata_id
 * @property int|null $palette_id
 * @property int|null $sphere_id
 * @property string $kommentar
 * @property float $sold_price
 * @property float|null $creditnote
 * @property-read \App\Models\Flow\Artikel $artikel
 * @property-read \App\Models\Flow\Karton|null $karton
 * @property-read \App\Models\Flow\Liferant $liferant
 * @property-read \App\Models\flow\Sphere|null $sphere
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Flow\Statistik[] $statistiks
 * @property-read int|null $statistiks_count
 * @method static Builder|KartonArtikel newModelQuery()
 * @method static Builder|KartonArtikel newQuery()
 * @method static Builder|KartonArtikel query()
 * @method static Builder|KartonArtikel whereArtikelId($value)
 * @method static Builder|KartonArtikel whereCreditnote($value)
 * @method static Builder|KartonArtikel whereEinkaufspreis($value)
 * @method static Builder|KartonArtikel whereEinkaufspreisRated($value)
 * @method static Builder|KartonArtikel whereGUID($value)
 * @method static Builder|KartonArtikel whereId($value)
 * @method static Builder|KartonArtikel whereImportSerial($value)
 * @method static Builder|KartonArtikel whereKartonDeliveredId($value)
 * @method static Builder|KartonArtikel whereKartonId($value)
 * @method static Builder|KartonArtikel whereKommentar($value)
 * @method static Builder|KartonArtikel whereLieferantId($value)
 * @method static Builder|KartonArtikel whereLieferantSortimentId($value)
 * @method static Builder|KartonArtikel whereLieferschein($value)
 * @method static Builder|KartonArtikel whereMetadataId($value)
 * @method static Builder|KartonArtikel wherePaletteId($value)
 * @method static Builder|KartonArtikel whereSeriennummer($value)
 * @method static Builder|KartonArtikel whereSoldPrice($value)
 * @method static Builder|KartonArtikel whereSphereId($value)
 * @method static Builder|KartonArtikel whereStatus($value)
 * @method static Builder|KartonArtikel whereUeberschuss($value)
 * @method static Builder|KartonArtikel whereVerloren($value)
 * @method static Builder|KartonArtikel whereZustand($value)
 */
class KartonArtikel extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';

    protected $table = 'flow_karton_artikel';

    public $timestamps = false;

    public function artikel()
    {
        return $this->belongsTo(Artikel::class, 'artikel_id', 'id');
    }

    public function statistiks()
    {
        return $this->HasMany(Statistik::class, 'karton_artikel_id', 'id');
    }

    public function karton()
    {
        return $this->belongsTo(Karton::class, 'karton_id', 'id');
    }

    public function sphere()
    {
        return $this->belongsTo(Sphere::class, 'sphere_id', 'id');
    }

    public function liferant()
    {
        return $this->belongsTo(Liferant::class, 'lieferant_id', 'id');
    }
}
