<?php

namespace Modules\LearningManagement\Repositories\Student;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\LearningManagement\Entities\EventTask;
use Modules\LearningManagement\Entities\QuestionAnswer;
use Modules\LearningManagement\Entities\TaskQuestion;
use Modules\LearningManagement\Entities\TaskSection;

class EventTaskRepository
{

    public function count(Collection $request = null): Int
    {
        $studentId = $request->get('user_id');
        return DB::table('event_tasks')
            ->join('tasks', 'tasks.id', 'event_tasks.task_id')
            ->when($request->get('search'), function ($q) use ($request) {
                $q->where('tasks.name', 'like', '%' . $request->get('search') . '%');
            })
            ->when($request->get('user_id'), function ($q) use ($studentId) {
                $q->join('event_participants', function ($join) use ($studentId) {
                    $join->on('event_participants.event_id', 'event_tasks.event_id');
                    $join->on('event_participants.user_id', DB::raw("'$studentId'"));
                });
            })
            ->when($request->get('event_id'), function ($q) use ($request) {
                $q->where('event_tasks.event_id', $request->get('event_id'));
            })
            ->join('events', 'events.id', 'event_tasks.event_id')
            ->count();
    }

    public function countTaskByResultStatus(Collection $request)
    {
        $possibleStatuses = ['Belum Dikerjakan', 'Sedang Dikerjakan', 'Belum Dikoreksi', 'Selesai'];
        $data = DB::table('event_participants')
            ->join('events', 'events.id', 'event_participants.event_id')
            ->join('event_tasks', 'event_tasks.event_id', 'events.id')
            ->leftJoin('task_results', function ($join) {
                $join->on('task_results.event_task_id', 'event_tasks.id');
                $join->on('task_results.user_id', 'event_participants.user_id');
            })
            ->when($request->get('user_id'), function ($q) use ($request) {
                $q->where('event_participants.user_id', $request->get('user_id'));
            })
            ->groupBy('task_results.status')
            ->select(
                DB::raw("COALESCE(task_results.status, 'Belum Dikerjakan') as status"),
                DB::raw('count(*) as count')
            )
            ->get();

        $statusCounts = [];
        foreach ($possibleStatuses as $status) {
            $statusCounts[$status] = 0;
        }
        foreach ($data as $result) {
            $statusCounts[$result->status] = $result->count;
        }

        $statusCounts = array_map(function ($count, $status) {
            return ['status' => $status, 'count' => $count];
        }, $statusCounts, array_keys($statusCounts));

        return $statusCounts;
    }

    public function getLatest(Collection $request, Int $limit = 10)
    {
        $studentId = $request->get('user_id');
        return DB::table('event_tasks')
            ->join('tasks', 'tasks.id', 'event_tasks.task_id')
            ->join('events', 'events.id', 'event_tasks.event_id')
            ->leftJoin('subjects', 'subjects.id', 'tasks.subject_id')
            ->when($request->get('search'), function ($q) use ($request) {
                $q->where('tasks.name', 'like', '%' . $request->get('search') . '%');
            })
            ->when($request->get('event_id'), function ($q) use ($request) {
                $q->where('event_tasks.event_id', $request->get('event_id'));
            })
            ->when($request->get('type'), function ($q) use ($request) {
                $q->where('tasks.type', $request->get('type'));
            })
            ->join('event_participants', function ($join) use ($studentId) {
                $join->on('event_participants.event_id', 'event_tasks.event_id');
                $join->on('event_participants.user_id', DB::raw("'$studentId'"));
            })
            ->leftJoin('task_results', function ($join) use ($studentId) {
                $join->on('task_results.event_task_id', 'event_tasks.id');
                $join->on('task_results.user_id', DB::raw("'$studentId'"));
            })
            ->select([
                'tasks.id',
                'event_tasks.id as event_task_id',
                'event_tasks.event_id',
                'tasks.name',
                'tasks.class',
                'subjects.name as subject',
                'events.name as event',
                'event_tasks.start_at',
                'event_tasks.end_at',
                DB::raw("COALESCE(task_results.status, 'Belum Dikerjakan') as status"),
                DB::raw("COALESCE(task_results.is_passed, null) as is_passed"),
                'task_results.score as score'
            ])
            ->when($request->get('status'), function ($q) use ($request) {
                if ($request->get('status') == 'Belum Dikerjakan') {
                    $q->whereNull('task_results.status');
                } else {
                    $q->where('task_results.status', $request->get('status'));
                }
            })
            ->orderBy('event_tasks.start_at', 'desc')
            ->take($limit)
            ->get();
    }

    public function getAll(Collection $request, $eventId = null, $studentId = null)
    {
        $now = now();
        return DB::table('event_tasks')
            ->join('tasks', 'tasks.id', 'event_tasks.task_id')
            ->leftJoin('subjects', 'subjects.id', 'tasks.subject_id')
            ->when($request->get('search'), function ($q) use ($request) {
                $q->where('tasks.name', 'like', '%' . $request->get('search') . '%');
            })
            ->when($request->get('event_id'), function ($q) use ($request) {
                $q->where('event_tasks.event_id', $request->get('event_id'));
            })
            ->when($eventId, function ($q) use ($eventId) {
                $q->where('event_tasks.event_id', $eventId);
            })
            ->when(!$request->get('event_id'), function ($q) use ($request) {
                $q->whereNotNull('event_participants.id');
            })
            ->leftJoin('event_participants', function ($join) use ($studentId) {
                $join->on('event_participants.event_id', 'event_tasks.event_id');
                $join->on('event_participants.user_id', DB::raw("'$studentId'"));
            })
            ->leftJoin('task_results', function ($join) use ($studentId) {
                $join->on('task_results.event_task_id', 'event_tasks.id');
                $join->on('task_results.user_id', DB::raw("'$studentId'"));
            })
            ->select([
                'event_tasks.*',
                'tasks.name',
                'tasks.description',
                'subjects.name as subject_name',
                'tasks.class',
                DB::raw("COALESCE(task_results.status, 'Belum Dikerjakan') as status"),
                DB::raw("COALESCE(task_results.is_passed, null) as is_passed"),
                'task_results.score as score',
                'task_results.created_at as answer_start_at',
                'task_results.finish_at as answer_finish_at'
            ])
            ->when($request->get('status'), function ($q) use ($request, $now) {
                if ($request->get('status') == 'Belum Dikerjakan') {
                    $q->whereNull('task_results.status')
                        ->where('event_tasks.start_at', '<=', $now)
                        ->where('event_tasks.end_at', '>=', $now);
                } else {
                    $q->where('task_results.status', $request->get('status'));
                }
            })
            ->orderBy('event_tasks.start_at', 'desc')
            ->get();
    }

    public function getAllByEventId(string $eventId, $studentId)
    {
        return DB::table('event_tasks')
            ->where('event_id', $eventId)
            ->join('tasks', 'tasks.id', 'event_tasks.task_id')
            ->join('events', 'events.id', 'event_tasks.event_id')
            ->leftJoin('subjects', 'subjects.id', 'tasks.subject_id')
            ->leftJoin('task_results', function ($join) use ($studentId) {
                $join->on('task_results.event_task_id', 'event_tasks.id');
                $join->on('task_results.user_id', DB::raw("'$studentId'"));
            })
            ->select([
                'event_tasks.*',
                'tasks.name',
                'tasks.description',
                'subjects.name as subject_name',
                DB::raw("COALESCE(task_results.status, 'Belum Dikerjakan') as status"),
                DB::raw("COALESCE(task_results.is_passed, null) as is_passed"),
                'task_results.score as score',
                'task_results.created_at as answer_start_at',
                'task_results.finish_at as answer_finish_at'
            ])
            ->get();
    }

    public function findById(String $taskId, $studentId)
    {
        return EventTask::query()
            ->where('event_tasks.id', $taskId)
            ->join('tasks', 'tasks.id', 'event_tasks.task_id')
            ->join('events', 'events.id', 'event_tasks.event_id')
            ->leftJoin('subjects', 'subjects.id', 'tasks.subject_id')
            ->leftJoin('event_participants', function ($join) use ($studentId) {
                $join->on('event_participants.event_id', 'events.id');
                $join->on('event_participants.user_id', DB::raw("'$studentId'"));
            })
            ->leftJoin('task_results', function ($join) use ($studentId) {
                $join->on('task_results.event_task_id', 'event_tasks.id');
                $join->on('task_results.user_id', DB::raw("'$studentId'"));
            })
            ->select([
                'event_tasks.*',
                'tasks.name',
                'tasks.description',
                'tasks.auto_correction',
                'subjects.name as subject_name',
                'events.name as event_name',
                'events.photo as event_photo',
                DB::raw("(CASE WHEN event_participants.id IS NOT NULL THEN 1 ELSE 0 END) AS is_owned"),
                DB::raw("COALESCE(task_results.status, 'Belum Dikerjakan') as status"),
                DB::raw("COALESCE(task_results.is_passed, null) as is_passed"),
                'task_results.id as event_task_result_id',
                'task_results.feedback as feedback',
                'task_results.score as score',
                'task_results.created_at as answer_start_at',
                'task_results.finish_at as answer_finish_at'
            ])
            ->withCount('taskSections', 'taskQuestions')
            ->first();
    }

    public function getTaskQuestionGroupBySection(String $taskId, $studentId)
    {
        return TaskSection::with([
            'taskQuestions.questionOptions',
            'taskQuestions.answer' => function ($q) use ($studentId) {
                $q->where('user_id', $studentId);
            }
        ])
            ->where('event_tasks.id', $taskId)
            ->select('task_sections.*')
            ->join('event_tasks', 'event_tasks.task_id', 'task_sections.task_id')
            ->join('events', 'events.id', 'event_tasks.event_id')
            ->leftJoin('task_results', function ($join) use ($studentId) {
                $join->on('task_results.event_task_id', 'event_tasks.id');
                $join->on('task_results.user_id', DB::raw("'$studentId'"));
            })
            ->oldest()
            ->get();
    }

    public function getTaskSubmissionQuestion(String $eventTaskId, $studentId)
    {
        return TaskQuestion::query()
            ->select([
                'task_questions.*',
                'questions.answer_type',
                'questions.question',
                'questions.file',
                'questions.file_name',
                'questions.explanation',
                'questions.level',
                'question_answers.id as answer_id',
                'question_answers.user_id as answer_user_id',
                'question_answers.task_question_id as answer_task_question_id',
                'question_answers.text as answer_text',
                'question_answers.question_option_id as answer_question_option_id',
                'question_answers.file as answer_file',
                'question_answers.file_name as answer_file_name',
                'question_answers.score as answer_score',
                'question_answers.is_correct as answer_is_correct',
                'question_answers.updated_at as answer_updated_at',
                'question_answers.created_at as answer_created_at'
            ])
            ->join('task_sections', 'task_sections.id', 'task_questions.task_section_id')
            ->join('event_tasks', 'event_tasks.task_id', 'task_sections.task_id')
            ->join('questions', 'questions.id', 'task_questions.question_id')
            ->leftJoin('question_answers', function ($join) use ($studentId) {
                $join->on('question_answers.task_question_id', 'task_questions.id');
                $join->on('question_answers.user_id', DB::raw("'$studentId'"));
            })
            ->where('event_tasks.id', $eventTaskId)
            ->first();
    }

    public function isCorrectAnswer($taskQuestionId, $optionId)
    {
        return TaskQuestion::where('task_questions.id', $taskQuestionId)
            ->join('question_options', 'question_options.question_id', 'task_questions.question_id')
            ->where('question_options.id', $optionId)
            ->where('question_options.is_correct', true)
            ->exists();
    }

    public function createEmptyAnswer(array $input)
    {
        return QuestionAnswer::insert($input);
    }

    public function createAnswer(Collection $input)
    {
        $isCorrect = null;
        if ($input->get('task_question_id')) {
            $isCorrect = $this->isCorrectAnswer($input->get('task_question_id'), $input->get('question_option_id'));
        }
        return QuestionAnswer::updateOrCreate([
            'user_id' => $input->get('user_id'),
            'task_question_id' => $input->get('task_question_id'),
        ], [
            'text' => $input->get('text'),
            'question_option_id' => $input->get('question_option_id'),
            'file' => $input->get('file'),
            'file_name' => $input->get('file_name'),
            'is_correct' => $isCorrect,
            'is_answered' => $input->get('question_option_id') || $input->get('text')
        ])->setHidden(['is_correct', 'score']);
    }

    public function setMark(Collection $input)
    {
        return QuestionAnswer::updateOrCreate([
            'user_id' => $input->get('user_id'),
            'task_question_id' => $input->get('task_question_id'),
        ], [
            'is_marked' => $input->get('mark')
        ]);
    }

    public function autoCorrection(String $taskId, String $userId, $correct, $incorrect, $empty)
    {
        QuestionAnswer::join('task_questions', 'task_questions.id', 'question_answers.task_question_id')
            ->join('task_sections', 'task_sections.id', 'task_questions.task_section_id')
            ->where('task_sections.task_id', $taskId)
            ->where('question_answers.user_id', $userId)
            ->update([
                'score' => DB::raw("CASE 
                                    WHEN is_answered = 0 THEN $empty
                                    WHEN is_correct = true THEN $correct
                                    WHEN is_correct = false THEN $incorrect
                                    ELSE 1
                                  END")
            ]);

        return QuestionAnswer::join('task_questions', 'task_questions.id', 'question_answers.task_question_id')
            ->join('task_sections', 'task_sections.id', 'task_questions.task_section_id')
            ->where('task_sections.task_id', $taskId)
            ->where('question_answers.user_id', $userId)
            ->sum('question_answers.score');
    }
}
