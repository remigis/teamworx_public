<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserPrivilege
 *
 * @property int $id
 * @property int $user_id
 * @property int $privilege_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserPrivilege newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPrivilege newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPrivilege query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPrivilege whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPrivilege whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPrivilege wherePrivilegeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPrivilege whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPrivilege whereUserId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Privilege|null $privilege
 */
class UserPrivilege extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'privilege_id'];

    public function privilege()
    {
        return $this->belongsTo(Privilege::class);
    }
}
