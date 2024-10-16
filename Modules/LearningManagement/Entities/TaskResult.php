<?php

namespace Modules\LearningManagement\Entities;

use App\Models\User;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Pds\Entities\Student;

class TaskResult extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'id',
        'event_task_id',
        'user_id',
        'score',
        'status',
        'is_passed',
        'is_can_access_result',
        'finish_at',
        'feedback'
    ];

    const statuses = [
        'Sedang Dikerjakan',
        'Belum Dikoreksi',
        'Sedang Dikoreksi',
        'Selesai'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'task_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function eventTask()
    {
        return $this->belongsTo(EventTask::class);
    }
}
