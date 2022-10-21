<?php

namespace App\Models\BoxScan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\BoxScan\Scan
 *
 * @property int $id
 * @property int $active
 * @property int $box_id
 * @property int $user_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Scan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Scan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Scan query()
 * @method static \Illuminate\Database\Eloquent\Builder|Scan whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scan whereBoxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scan whereUserId($value)
 * @mixin \Eloquent
 */
class Scan extends Model
{
    use HasFactory;
}
