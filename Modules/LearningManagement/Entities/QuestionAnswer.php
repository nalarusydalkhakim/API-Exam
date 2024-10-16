<?php

namespace Modules\LearningManagement\Entities;

use App\Models\User;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Pds\Entities\Student;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class QuestionAnswer extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'id',
        'task_question_id',
        'user_id',
        'is_marked',
        'text',
        'question_option_id',
        'file',
        'file_name',
        'score',
        'is_correct',
        'is_answered',
        'feedback'
    ];

    protected $casts = [
        'is_marked' => 'boolean',
        'is_correct' => 'boolean',
        'is_answered' => 'boolean',
        'score' => 'float'
    ];

    public function taskQuestion()
    {
        return $this->belongsTo(TaskQuestion::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function questionOption()
    {
        return $this->belongsTo(QuestionOption::class);
    }

    protected function file(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Storage::url($value) : null,
        );
    }
}
