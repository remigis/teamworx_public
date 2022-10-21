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
 * @property int $metadata_id
 * @property string|null $email E-Mail
 * @property string|null $name Name
 * @property string|null $password
 * @property int|null $score Score
 * @property int|null $lieferant_id
 * @property-read \App\Models\Flow\Statistik $statistik
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereLieferantId($value)
 * @method static Builder|User whereMetadataId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereScore($value)
 */
class User extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';

    protected $table = 'flow_user';

    public function statistik()
    {
        return $this->belongsTo(Statistik::class, 'id', 'user_id');
    }
}
