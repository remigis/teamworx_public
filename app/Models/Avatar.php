<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Avatar
 *
 * @property int $id
 * @property int $user_id
 * @property string $path
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static Builder|Avatar newModelQuery()
 * @method static Builder|Avatar newQuery()
 * @method static Builder|Avatar query()
 * @method static Builder|Avatar whereCreatedAt($value)
 * @method static Builder|Avatar whereId($value)
 * @method static Builder|Avatar whereName($value)
 * @method static Builder|Avatar wherePath($value)
 * @method static Builder|Avatar whereUpdatedAt($value)
 * @method static Builder|Avatar whereUserId($value)
 * @mixin \Eloquent
 */
class Avatar extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
