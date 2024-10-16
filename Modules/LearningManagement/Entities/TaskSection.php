<?php

namespace Modules\LearningManagement\Entities;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSection extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'id',
        'task_id',
        'name',
        'description'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    public function taskQuestions()
    {
        return $this->hasMany(TaskQuestion::class)
            ->orderBy('number', 'asc')
            ->orderBy('created_at', 'asc')
            ->join('questions', 'questions.id', '=', 'task_questions.question_id')
            ->select([
                'task_questions.*',
                'questions.answer_type',
                'questions.question',
                'questions.file',
                'questions.file_name',
                'questions.explanation',
                'questions.level',
                'questions.visibility',
                'questions.owner_id as question_owner_id'
            ]);
    }
}
