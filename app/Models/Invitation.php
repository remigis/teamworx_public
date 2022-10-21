<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Models\Invitation
 *
 * @property int $id
 * @property string $email
 * @property string $name
 * @property string $token
 * @property string $invited_by
 * @property string|null $registered_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereInvitedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereRegisteredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Invitation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email'];


    public function generateToken()
    {
        $this->token = Str::random(50);
    }
}
