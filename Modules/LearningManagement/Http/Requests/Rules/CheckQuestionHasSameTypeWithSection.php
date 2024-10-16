<?php

namespace Modules\LearningManagement\Http\Requests\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\LearningManagement\Repositories\Base\QuestionRepository;
use Modules\LearningManagement\Repositories\Base\TaskSectionRepository;

class CheckQuestionHasSameTypeWithSection implements ValidationRule, DataAwareRule
{
    protected $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $taskSectionRepository = new TaskSectionRepository;
        $taskSection = $taskSectionRepository->findById($this->data['sections'][explode('.', $attribute)[1]]['id'] ?? null);
        if ($taskSection) {
            $answerType = $this->data['sections'][explode('.', $attribute)[1]]['questions'][explode('.', $attribute)[3]]['answer_type'] ?? null;

            if (!$answerType) {
                $questionRepository = new QuestionRepository;
                $question = $questionRepository->findById($value);
                $answerType = $question->answer_type ?? null;
                if (!$answerType) {
                    $fail('Soal tidak ditemukan');
                }
            }
        } else {
            $fail('Bagian soal tidak ditemukan');
        }
    }
}
