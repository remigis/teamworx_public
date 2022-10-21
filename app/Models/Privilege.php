<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Privilege
 *
 * @property int $id
 * @property string $name
 * @property string $text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege query()
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserPrivilege[] $userPrivileges
 * @property-read int|null $user_privileges_count
 */
class Privilege extends Model
{
    use HasFactory;

    public function userPrivileges()
    {
        return $this->hasMany(UserPrivilege::class);
    }

}
