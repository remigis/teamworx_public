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
 * @property string|null $name Artikelname
 * @property string|null $artikelnummer Artikelnummer
 * @property string $artikel_typ
 * @property string|null $hersteller Hersteller
 * @property string $preis
 * @property int|null $plenty_a_id Plenty ID (Zustand A)
 * @property int|null $plenty_b_id Plenty ID (Zustand B)
 * @property int|null $plenty_c_id Plenty ID (Zustand C)
 * @property string|null $plenty_a_mouseover_text Zustandsinformation (Zustand A)
 * @property string|null $plenty_b_mouseover_text Zustandsinformation (Zustand B)
 * @property string|null $plenty_c_mouseover_text Zustandsinformation (Zustand C)
 * @property int|null $plenty_a_bestand Bestand (Zustand A)
 * @property int|null $plenty_b_bestand Bestand (Zustand B)
 * @property int|null $plenty_c_bestand Bestand (Zustand C)
 * @property string|null $mouseover_text Artikelinformation (Allgemein)
 * @property string $asin
 * @property string|null $xasin_a Amazon XASIN (Zustand A)
 * @property string|null $xasin_b Amazon XASIN (Zustand B)
 * @property string|null $xasin_c Amazon XASIN (Zustand C)
 * @property int $metadata_id
 * @property int|null $plenty_a_warehouse
 * @property int|null $plenty_b_warehouse
 * @property int|null $plenty_c_warehouse
 * @property int $reparierbar
 * @property int $drucken
 * @property int|null $plenty_a_price_id
 * @property int|null $plenty_b_price_id
 * @property int|null $plenty_c_price_id
 * @property string|null $EAN1
 * @property string|null $EAN2
 * @property string|null $EAN3
 * @property string|null $EAN4
 * @property int|null $store_a
 * @property int|null $store_b
 * @property int|null $store_c
 * @property string $HeightInMM
 * @property string $LengthInMM
 * @property string $WidthInMM
 * @property string $WeightInGramm
 * @property string|null $answer_ids
 * @property int $old_ABC_logic
 * @property string $additionals
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Flow\KartonArtikel[] $kartonArtikels
 * @property-read int|null $karton_artikels_count
 * @method static Builder|Artikel newModelQuery()
 * @method static Builder|Artikel newQuery()
 * @method static Builder|Artikel query()
 * @method static Builder|Artikel whereAdditionals($value)
 * @method static Builder|Artikel whereAnswerIds($value)
 * @method static Builder|Artikel whereArtikelTyp($value)
 * @method static Builder|Artikel whereArtikelnummer($value)
 * @method static Builder|Artikel whereAsin($value)
 * @method static Builder|Artikel whereDrucken($value)
 * @method static Builder|Artikel whereEAN1($value)
 * @method static Builder|Artikel whereEAN2($value)
 * @method static Builder|Artikel whereEAN3($value)
 * @method static Builder|Artikel whereEAN4($value)
 * @method static Builder|Artikel whereHeightInMM($value)
 * @method static Builder|Artikel whereHersteller($value)
 * @method static Builder|Artikel whereId($value)
 * @method static Builder|Artikel whereLengthInMM($value)
 * @method static Builder|Artikel whereMetadataId($value)
 * @method static Builder|Artikel whereMouseoverText($value)
 * @method static Builder|Artikel whereName($value)
 * @method static Builder|Artikel whereOldABCLogic($value)
 * @method static Builder|Artikel wherePlentyABestand($value)
 * @method static Builder|Artikel wherePlentyAId($value)
 * @method static Builder|Artikel wherePlentyAMouseoverText($value)
 * @method static Builder|Artikel wherePlentyAPriceId($value)
 * @method static Builder|Artikel wherePlentyAWarehouse($value)
 * @method static Builder|Artikel wherePlentyBBestand($value)
 * @method static Builder|Artikel wherePlentyBId($value)
 * @method static Builder|Artikel wherePlentyBMouseoverText($value)
 * @method static Builder|Artikel wherePlentyBPriceId($value)
 * @method static Builder|Artikel wherePlentyBWarehouse($value)
 * @method static Builder|Artikel wherePlentyCBestand($value)
 * @method static Builder|Artikel wherePlentyCId($value)
 * @method static Builder|Artikel wherePlentyCMouseoverText($value)
 * @method static Builder|Artikel wherePlentyCPriceId($value)
 * @method static Builder|Artikel wherePlentyCWarehouse($value)
 * @method static Builder|Artikel wherePreis($value)
 * @method static Builder|Artikel whereReparierbar($value)
 * @method static Builder|Artikel whereStoreA($value)
 * @method static Builder|Artikel whereStoreB($value)
 * @method static Builder|Artikel whereStoreC($value)
 * @method static Builder|Artikel whereWeightInGramm($value)
 * @method static Builder|Artikel whereWidthInMM($value)
 * @method static Builder|Artikel whereXasinA($value)
 * @method static Builder|Artikel whereXasinB($value)
 * @method static Builder|Artikel whereXasinC($value)
 */
class Artikel extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';

    protected $table = 'flow_artikel';

    public function kartonArtikels()
    {
        return $this->hasMany(KartonArtikel::class, 'artikel_id', 'id');
    }
}
