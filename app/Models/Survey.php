<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int|mixed|string|null $owner_id
 * @property string $question
 * @property string $description
 * @property boolean $is_anonymous
 * @property boolean $is_multiple
 * @property boolean $is_activated
 * @method static findOrFail(\Ramsey\Uuid\Type\Integer $id)
 * @method static where(string $string, $ownerId)
 */
class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'question',
        'description',
        'is_anonymous',
        'is_multiple',
        'is_activated',
    ];

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'surveys_id');
    }
}
