<?php

namespace App\Models;

use App\Models\BoxScan\ScannedGoodsFlowId;
use App\Models\NewItemScan\ScannedSn;
use App\Utilities\PrivilegeUtilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

/**
 * @property $id
 * @property $name
 * @property $email
 * @property $password
 * @property $remember_token
 * @property $created_at
 * @property $updated_at
 * @property $email_verified_at
 */

/**
 * Post
 *
 * @mixin Builder
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Avatar|null $avatar
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|ScannedGoodsFlowId[] $scannedGoodsFlow
 * @property-read int|null $scanned_goods_flow_count
 * @property-read \App\Models\UserVoice|null $userVoice
 * @property-read \Illuminate\Database\Eloquent\Collection|ScannedSn[] $scannedSns
 * @property-read int|null $scanned_sns_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserPrivilege[] $privileges
 * @property-read int|null $privileges_count
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getPrivileges(): array
    {
        return PrivilegeUtilities::getUserPrivileges(Auth::user());
    }

    public function havePrivilegeTo(string|array $action): bool
    {
        $allow = false;

        if (is_string($action)) {
            $allow = in_array($action, $this->getPrivileges());
        }

        if (is_array($action)) {
            $allow = !((count(array_diff($action, $this->getPrivileges())) > 0));
        }

        return $allow;
    }

    public function avatar()
    {
        return $this->hasOne(Avatar::class);
    }

    public function scannedGoodsFlow()
    {
        return $this->hasMany(ScannedGoodsFlowId::class);
    }

    public function scannedSns()
    {
        return $this->hasMany(ScannedSn::class);
    }

    public function userVoice()
    {
        return $this->hasOne(UserVoice::class);
    }

    public function privileges()
    {
        return $this->hasMany(UserPrivilege::class);
    }
}
