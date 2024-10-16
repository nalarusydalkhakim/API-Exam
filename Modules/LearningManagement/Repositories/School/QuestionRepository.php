<?php

namespace Modules\LearningManagement\Repositories\School;

use Illuminate\Support\Collection;
use Modules\LearningManagement\Repositories\Base\QuestionRepository as BaseQuestionRepository;

class QuestionRepository extends BaseQuestionRepository
{
    protected $questionQuery;

    public function simpleSelect()
    {
        $this->questionQuery = $this->questionQuery
            ->with('owner:id,name,email,photo', 'options', 'subject')
            ->select([
                'questions.*'
            ]);
        return $this;
    }

    public function detailSelect()
    {
        $this->questionQuery = $this->questionQuery
            ->with('owner:id,name,email,photo', 'options', 'subject')
            ->select([
                'questions.*'
            ]);
        return $this;
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
}
