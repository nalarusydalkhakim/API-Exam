<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (Hash::check($this->old_password, $this->user()->password)) {
            $this->merge([
                'check' => $this->old_password
            ]);
        }
        return [
            'old_password' => 'required|same:check',
            'new_password' => 'required|min:6|max:30|different:old_password',
            'new_password_confirmation' => 'required|same:new_password'
        ];
    }
    public function messages()
    {
        return [
            'old_password.same' => 'Password lama tidak cocok',
        ];
    }
}
