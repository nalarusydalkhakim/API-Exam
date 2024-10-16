<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'organization' => 'nullable|string|max:200',
            'phone' => 'nullable|numeric|digits_between:5,20',
            'gender' => 'nullable|string|in:Laki-laki,Perempuan',
            'village_id' => 'nullable|exists:id_villages,id',
            'address' => 'nullable|string|max:200',
            'class' => 'nullable|string|max:200',
        ];
    }
}
