<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $answer
 * @property int|mixed|string $surveys_id
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
        'surveys_id'
    ];


    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class, 'surveys_id');
    }
}
