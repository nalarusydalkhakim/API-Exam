<?php

namespace Modules\LearningManagement\Entities;

use App\Models\User;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'id',
        'owner_id',
        'name',
        'description',
        'visibility',
        'class',
        'subject_id',
        'auto_correction',
        'created_at',
        'updated_at'
    ];

    const visibilities = [
        'Hanya Saya',
        'Publik'
    ];

    public function taskSections()
    {
        return $this->hasMany(TaskSection::class);
    }
    public function taskQuestions()
    {
        return $this->hasManyThrough(TaskQuestion::class, TaskSection::class);
    }
    public function eventTasks()
    {
        return $this->hasMany(EventTask::class, 'task_id', 'id');
    }
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }
}
