<?php

namespace Modules\LearningManagement\Entities;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'id',
        'question_id',
        'key',
        'option',
        'is_correct'
    ];

    const keys = [
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Z',
        'Z'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
