<?php

namespace Modules\LearningManagement\Services\Base;

use Carbon\Carbon;
use Modules\LearningManagement\Repositories\Base\EventTaskRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\LearningManagement\Repositories\Base\TaskRepository;
use Illuminate\Support\Facades\DB;
use Modules\LearningManagement\Repositories\School\ClassSubjectRepository;
use Modules\LearningManagement\Repositories\School\EventRepository;
use Modules\LearningManagement\Repositories\School\TaskSectionRepository;

class EventTaskService
{
    protected $eventTaskRepository, $taskRepository;

    public function __construct()
    {
        $this->initRepository();
    }

    protected function initRepository()
    {
        $this->eventTaskRepository = new EventTaskRepository;
        $this->taskRepository = new TaskRepository;
    }

    public function getAll(Request $request = null)
    {
        $data = $this->eventTaskRepository->getAll($request->all());
        return $data;
    }

    public function getAllByEventId($eventId)
    {
        return $this->eventTaskRepository->getAllByEventId($eventId);
    }

    public function getPaginate(Request $request = null, $eventId)
    {
        $request->query->add([
            'event_id' => $eventId
        ]);
        $data = $this->eventTaskRepository->getPaginate($request->all(), $eventId);
        return $data;
    }

    public function findById(String $id = null, $eventId = null)
    {
        $data = $this->eventTaskRepository->findById($id);
        return $data;
    }

    public function create(Request $requestTask, Request $requestEventTask, String $teacherId = null)
    {
        try {
            DB::beginTransaction();
            $inputTask = collect($requestTask->validated())->except('sections');
            $inputTask->put('owner_id', $teacherId);
            $taskId = $this->taskRepository->create($inputTask);
            $taskSections = collect($requestTask->validated())->get('sections');
            $now = Carbon::now();
            if ($taskSections ? count($taskSections) : false) {
                $inputTaskSections = [];
                foreach ($taskSections as $taskSection) {
                    $inputTaskSections[] = array_merge($taskSection, [
                        'id' => Str::uuid()->toString(),
                        'task_id' => $taskId,
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);
                }
                $taskSectionRepository = new TaskSectionRepository;
                $taskSectionRepository->createBulk(collect($inputTaskSections));
            }
            $inputEventTask = collect($requestEventTask->validated());
            $inputEventTask->put('id', Str::uuid()->toString());
            $inputEventTask->put('task_id', $taskId);
            $inputEventTask->put('created_at', now());
            $inputEventTask->put('updated_at', now());
            if ($this->eventTaskRepository->create($inputEventTask)) {
                DB::commit();
                $this->initRepository();
                return $this->findById($inputEventTask->get('id'));
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function attachToEvent(Request $request)
    {
        try {
            $inputEventTask = collect($request->validated());
            $inputEventTask = collect($inputEventTask->get('tasks', []))->map(function ($eventTask) use ($inputEventTask) {
                $eventTask = collect($eventTask);
                return [
                    'id' => Str::uuid()->toString(),
                    'event_id' => $inputEventTask->get('event_id'),
                    'task_id' => $eventTask->get('task_id'),
                    'start_at' => $eventTask->get('start_at'),
                    'end_at' => $eventTask->get('end_at'),
                    'point_correct' => $eventTask->get('point_correct'),
                    'point_incorrect' => $eventTask->get('point_incorrect'),
                    'point_empty' => $eventTask->get('point_empty'),
                ];
            });
            return $this->eventTaskRepository->create($inputEventTask);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function attachToEvents(Request $request)
    {
        try {
            $inputEventTask = collect($request->validated());
            $inputEventTask = collect($inputEventTask->get('events', []))->map(function ($eventTask) use ($inputEventTask) {
                $eventTask = collect($eventTask);
                return [
                    'id' => Str::uuid()->toString(),
                    'task_id' => $inputEventTask->get('task_id'),
                    'event_id' => $eventTask->get('event_id'),
                    'start_at' => $eventTask->get('start_at'),
                    'end_at' => $eventTask->get('end_at'),
                    'point_correct' => $eventTask->get('point_correct'),
                    'point_incorrect' => $eventTask->get('point_incorrect'),
                    'point_empty' => $eventTask->get('point_empty'),
                ];
            });
            return $this->eventTaskRepository->create($inputEventTask);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update($eventTaskId, Request $request)
    {
        try {
            if ($this->eventTaskRepository->update(
                $eventTaskId,
                collect($request->validated())
            )) {
                $this->initRepository();
                return $this->findById($eventTaskId);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete(String $id)
    {
        return $this->eventTaskRepository->delete($id);
    }
}
