<?php

namespace Modules\LearningManagement\Repositories\Base;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\LearningManagement\Entities\QuestionOption;

class QuestionOptionRepository
{
    protected $questionOptionQuery;
    public function __construct()
    {
        $this->questionOptionQuery = new QuestionOption;
    }

    public function findById($id)
    {
        $this->detailSelect();
        return $this->questionOptionQuery
            ->where('question_options.id', $id)
            ->first();
    }

    public function create(Collection $input)
    {
        return $this->questionOptionQuery
            ->insert($input->all());
    }

    public function update($id, Collection $input)
    {
        $input->put('updated_at', now());
        return $this->questionOptionQuery
            ->where('question_options.id', $id)
            ->update($input->all());
    }

    public function delete($id)
    {
        return $this->questionOptionQuery
            ->where('id', $id)
            ->delete();
    }

    public function deleteBulk(array $ids)
    {
        return $this->questionOptionQuery
            ->whereIn('question_options.id', $ids)
            ->delete();
    }

    public function deleteWhereNotIn(String $questionId, array $ids)
    {
        return $this->questionOptionQuery
            ->where('question_options.question_id', $questionId)
            ->whereNotIn('question_options.id', $ids)
            ->delete();
    }

    public function simpleSelect()
    {
        $this->questionOptionQuery = $this->questionOptionQuery
            ->select([
                'question_options.*'
            ]);
        return $this;
    }

    public function detailSelect()
    {
        $this->questionOptionQuery = $this->questionOptionQuery
            ->select([
                'question_options.*'
            ]);
        return $this;
    }

    public function setCorrectAnswer(String $questionId, String $optionId)
    {
        $this->questionOptionQuery
            ->where('question_options.question_id', $questionId)
            ->update(['is_correct' => false]);
        return $this->questionOptionQuery
            ->where('question_options.id', $optionId)
            ->where('question_options.question_id', $questionId)
            ->update(['is_correct' => true]);
    }
}
