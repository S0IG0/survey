<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $answer
 * @property int|mixed|string $surveys_id
 * @property Survey|mixed $survey
 * @method static findOrFail($id)
 */
class Answer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'answer',
    ];

    protected $hidden = [
        'surveys_id'
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class, 'surveys_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_answer');
    }
}
