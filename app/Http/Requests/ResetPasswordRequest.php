<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class ResetPasswordRequest extends FormRequest
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
        $check = DB::table('password_resets')->where('email', $this->email)->first();
        $check ? $token = $check->token : $token = null;
        return [
            'email' => 'required|string|email|exists:password_resets',
            'token' => 'required|in:'.$token,
            'password' => 'required|min:6|max:100',
            'password_confirmation' => 'required|same:password'
        ];
    }
    public function messages()
    {
        return [
            'email.required' => 'Email tidak boleh kosong',
            'email.exists' => 'Email yang dimasukkan tidak meminta pergantian password',
            'token.required' => 'Token tidak boleh kosong',
            'email.required' => 'Email tidak boleh kosong',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password tidak boleh kurang dari 6 karakter',
            'password.max' => 'Password tidak boleh lebih dari 100 karakter',
            'password_confirmation.required' => 'Konfirmasi password tidak boleh kosong',
            'password_confirmation.same' => 'Konfirmasi password harus sama dengan password',
        ];
    }
}
