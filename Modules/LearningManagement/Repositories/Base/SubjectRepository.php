<?php

namespace Modules\LearningManagement\Repositories\Base;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\LearningManagement\Entities\Subject;

class SubjectRepository
{
    protected $subjectQuery;
    public function __construct()
    {
        $this->subjectQuery = new Subject();
    }

    public function getAll(array $request)
    {
        $this->filter($request)->simpleSelect()->order($request);
        return $this->subjectQuery
            ->get();
    }

    public function getPaginate(array $request): LengthAwarePaginator
    {
        $this->filter($request)->simpleSelect()->order($request);
        return $this->subjectQuery
            ->paginate($request['per_page'] ?? 10)
            ->appends($request);
    }

    public function findById($id)
    {
        $this->detailSelect();
        return $this->subjectQuery
            ->where('subjects.id', $id)
            ->first();
    }

    public function create(Collection $input)
    {
        $input->put('id', $input->get('id') ?? Str::uuid()->toString());
        $input->put('created_at', now());
        $input->put('updated_at', $input->get('created_at'));
        $this->subjectQuery
            ->insert($input->all());
        return $input->get('id');
    }

    public function update($id, Collection $input)
    {
        $input->put('updated_at', now());
        return $this->subjectQuery
            ->where('subjects.id', $id)
            ->update($input->all());
    }

    public function delete($id)
    {
        return $this->subjectQuery
            ->where('subjects.id', $id)
            ->delete();
    }

    public function filter(array $request, String $userId = null)
    {
        $filter = collect($request);
        $this->subjectQuery = $this->subjectQuery
            ->when($filter->get('search'), function ($q) use ($filter) {
                $q->where(function ($q) use ($filter) {
                    $q->Where('subjects.name', 'like', '%' . $filter->get('search') . '%');
                });
            })->when($filter->get('name'), function ($q) use ($filter) {
                $q->Where('subjects.name', 'like', '%' . $filter->get('name') . '%');
            });
        return $this;
    }


    private function order(array $request)
    {
        $this->subjectQuery = $this->subjectQuery->when(isset($request['order_field']) && isset($request['order_direction']), function ($q) use ($request) {
            $q->orderBy($request['order_field'], $request['order_direction']);
        })
            ->when(!isset($request['order_field']) || !isset($request['order_direction']), function ($q) use ($request) {
                $q->orderBy('subjects.created_at', 'asc');
            });
    }

    public function simpleSelect()
    {
        $this->subjectQuery = $this->subjectQuery
            ->select([
                'subjects.*'
            ]);
        return $this;
    }

    public function detailSelect()
    {
        $this->subjectQuery = $this->subjectQuery
            ->select([
                'subjects.*'
            ]);
        return $this;
    }
}
