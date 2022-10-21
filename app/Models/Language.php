<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Language
 *
 * @property int $id
 * @property string $language
 * @property string $alt
 * @property string $tag
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|Language newModelQuery()
 * @method static Builder|Language newQuery()
 * @method static Builder|Language query()
 * @method static Builder|Language whereAlt($value)
 * @method static Builder|Language whereCreatedAt($value)
 * @method static Builder|Language whereId($value)
 * @method static Builder|Language whereLanguage($value)
 * @method static Builder|Language whereTag($value)
 * @method static Builder|Language whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Language extends Model
{
    use HasFactory;
}
