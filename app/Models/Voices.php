<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Voices
 *
 * @property int $id
 * @property string $name
 * @property string $label
 * @property string $location
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Voices newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Voices newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Voices query()
 * @method static \Illuminate\Database\Eloquent\Builder|Voices whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voices whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voices whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voices whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voices whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voices whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Voices extends Model
{
    use HasFactory;
}
