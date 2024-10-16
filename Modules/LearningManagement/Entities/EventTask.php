<?php

namespace Modules\LearningManagement\Entities;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Modules\Event\Entities\Event;

class EventTask extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'id',
        'event_id',
        'task_id',
        'start_at',
        'end_at',
        'point_correct',
        'point_incorrect',
        'point_empty',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    public function taskSections()
    {
        return $this->hasMany(TaskSection::class, 'task_id', 'task_id');
    }
    public function taskEvents()
    {
        return $this->hasMany(EventTask::class, 'task_id', 'task_id');
    }
    public function taskQuestions()
    {
        return $this->hasManyThrough(
            TaskQuestion::class,
            TaskSection::class,
            'task_id',
            'task_section_id',
            'task_id',
            'id'
        );
    }
    public function taskResults()
    {
        return $this->hasMany(TaskResult::class, 'event_task_id', 'id');
    }
}
