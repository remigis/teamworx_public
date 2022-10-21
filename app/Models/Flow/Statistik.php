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
 * @property int $user_id Betreffender Nutzer
 * @property int $timestamp
 * @property int $karton_artikel_id
 * @property string|null $zustand
 * @property string|null $action
 * @property int $seconds
 * @property-read \App\Models\Flow\KartonArtikel $kartonArtikel
 * @property-read \App\Models\Flow\User|null $user
 * @method static Builder|Statistik newModelQuery()
 * @method static Builder|Statistik newQuery()
 * @method static Builder|Statistik query()
 * @method static Builder|Statistik whereAction($value)
 * @method static Builder|Statistik whereId($value)
 * @method static Builder|Statistik whereKartonArtikelId($value)
 * @method static Builder|Statistik whereSeconds($value)
 * @method static Builder|Statistik whereTimestamp($value)
 * @method static Builder|Statistik whereUserId($value)
 * @method static Builder|Statistik whereZustand($value)
 */
class Statistik extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';

    protected $table = 'flow_statistik';

    public function kartonArtikel()
    {
        return $this->belongsTo(KartonArtikel::class, 'karton_artikel_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
