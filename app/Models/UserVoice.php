<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\UserVoice
 *
 * @property int $id
 * @property int $user_id
 * @property string $voice_name
 * @property int $pitch
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserVoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserVoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserVoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserVoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserVoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserVoice wherePitch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserVoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserVoice whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserVoice whereVoiceName($value)
 * @mixin \Eloquent
 */
class UserVoice extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
