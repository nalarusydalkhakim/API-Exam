<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

class VerificationTokenCheckRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'token' => [
                'required',
                Rule::exists('verification_tokens')->where(function (Builder $query) {
                    return $query->where('user_id', $this->user()->id);
                }),
            ]
        ];
    }
}
