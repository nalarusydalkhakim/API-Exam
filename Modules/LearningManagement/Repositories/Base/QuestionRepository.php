<?php

namespace Modules\LearningManagement\Repositories\Base;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\LearningManagement\Entities\Question;

class QuestionRepository
{
    protected $questionQuery;
    public function __construct()
    {
        $this->questionQuery = new Question();
    }

    public function count(Collection $request, String $schoolId = null): Int
    {
        $this->filter($request->all(), $schoolId)->simpleSelect();
        return $this->questionQuery
            ->count();
    }

    public function countQuestionByLevel(Collection $request, $schoolId = null)
    {
        $this->questionQuery = new Question();
        $this->filter($request->all(), $schoolId);
        return $this->questionQuery
            ->groupBy('questions.level')
            ->select(
                'questions.level',
                DB::raw('count(*) as count')
            )
            ->get();
    }

    public function getAll(array $request, String $schoolId = null)
    {
        $this->filter($request, $schoolId)->simpleSelect()->order($request);;
        return $this->questionQuery
            ->all();
    }

    public function getPaginate(array $request, String $ownerId = null): LengthAwarePaginator
    {
        $this->filter($request, $ownerId)->simpleSelect()->order($request);
        return $this->questionQuery
            ->paginate($request['per_page'] ?? 10)
            ->appends($request);
    }

    public function findById($id)
    {
        $this->detailSelect();
        return $this->questionQuery
            ->where('questions.id', $id)
            ->first();
    }

    public function create(Collection $input)
    {
        $input->put('id', $input->get('id') ?? Str::uuid()->toString());
        $input->put('created_at', now());
        $input->put('updated_at', $input->get('created_at'));
        $this->questionQuery
            ->create($input->all());
        return $input->get('id');
    }

    public function update($id, Collection $input, String $teacherId = null)
    {
        $input->put('updated_at', now());
        return $this->questionQuery
            ->where('questions.id', $id)
            ->when($teacherId, function ($q) use ($teacherId) {
                $q->where('questions.owner_id', $teacherId);
            })
            ->update($input->all());
    }

    public function delete($id, String $teacherId = null)
    {
        return $this->questionQuery
            ->where('questions.id', $id)
            ->when($teacherId, function ($q) use ($teacherId) {
                $q->where('questions.owner_id', $teacherId);
            })
            ->delete();
    }

    public function filter(array $request, String $ownerId = null)
    {
        $filter = collect($request);
        $this->questionQuery = $this->questionQuery
            ->when($filter->get('search'), function ($q) use ($filter) {
                $q->where(function ($q) use ($filter) {
                    $q->Where('questions.question', 'like', '%' . $filter->get('search') . '%');
                });
            })->when($filter->get('answer_type'), function ($q) use ($filter) {
                $q->Where('questions.answer_type', $filter->get('answer_type'));
            })->when($filter->get('level'), function ($q) use ($filter) {
                $q->Where('questions.level', $filter->get('level'));
            })->when($filter->get('subject_id'), function ($q) use ($filter) {
                $q->Where('questions.subject_id', $filter->get('subject_id'));
            })->when($filter->get('class'), function ($q) use ($filter) {
                $q->Where('questions.class', $filter->get('class'));
            })->when($filter->get('teacher_id'), function ($q) use ($filter) {
                $q->Where('questions.teacher_id', $filter->get('teacher_id'));
            })->when($ownerId && !$filter->get('visibility'), function ($q) use ($ownerId) {
                $q->Where('questions.owner_id', $ownerId);
            })->when($filter->get('visibility'), function ($q) use ($filter) {
                $q->when($filter->get('visibility') == 'mine', function ($q) use ($filter) {
                    $q->Where('questions.owner_id', $filter->get('user_id'));
                });
                $q->when($filter->get('visibility') == 'public', function ($q) {
                    $q->Where('questions.visibility', 'Publik');
                });
            });
        return $this;
    }

    private function order(array $request)
    {
        $this->questionQuery = $this->questionQuery->when(isset($request['order_field']) && isset($request['order_direction']), function ($q) use ($request) {
            $q->orderBy($request['order_field'], $request['order_direction']);
        })
            ->when(!isset($request['order_field']) || !isset($request['order_direction']), function ($q) use ($request) {
                $q->orderBy('questions.created_at', 'asc');
            });
    }

    public function simpleSelect()
    {
        return $this;
    }

    public function detailSelect()
    {
        return $this;
    }
}
