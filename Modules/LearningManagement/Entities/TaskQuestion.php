<?php

namespace Modules\LearningManagement\Entities;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskQuestion extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'id',
        'question_id',
        'task_section_id',
        'number'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
    public function taskSection()
    {
        return $this->belongsTo(TaskSection::class);
    }
    public function questionOptions()
    {
        return $this->hasMany(QuestionOption::class, 'question_id', 'question_id')
            ->orderBy('key', 'asc');
    }
    public function answers()
    {
        return $this->hasMany(QuestionAnswer::class, 'task_question_id');
    }
    public function answer()
    {
        return $this->hasOne(QuestionAnswer::class, 'task_question_id');
    }
}
