<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\VerificationTokenCheckRequest;
use App\Events\RequestEmailVerification;
use App\Events\EmailVerified;
use App\Events\PasswordChanged;
use App\Http\Requests\UpdateProfileRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    public function me(Request $request)
    {
        $data = $request->user()->load(['profile', 'roles']);

        if ($data->status == 'inactive') {
            return $this->error('Akun Anda tidak aktif!', 403);
        } else if ($data->status == 'banned') {
            return $this->error('Akun Anda tidak diblokir!', 403);
        }
        $data->permissions = $this->getLoginUserPermission($request);
        return $this->ok($data, 'Berhasil mengambil data user');
    }

    private function getLoginUserPermission(Request $request)
    {
        return DB::table('permissions')
            ->join('role_has_permissions', 'role_has_permissions.permission_id', 'permissions.id')
            ->join('model_has_roles', 'model_has_roles.role_id', 'role_has_permissions.role_id')
            ->where('model_has_roles.model_uuid', $request->user()->id)
            ->select('permissions.id', 'permissions.name')
            ->groupBy('permissions.id')
            ->pluck('permissions.name');
    }

    public function updateAccount(UpdateAccountRequest $requestUser, UpdateProfileRequest $requestProfile)
    {
        $inputUser = $requestUser->validated();

        if ($requestUser->has('photo')) {
            $inputUser['photo'] = $requestUser->file('photo')->store('avatars');
        }

        $requestUser->user()->update($inputUser);

        $requestUser->user()->profile()->update($requestProfile->validated());

        return $this->ok($requestUser->user()->load('profile', 'roles'), 'Berhasil mengubah profil');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $request->user()->update([
            'password' => Hash::make($request->new_password)
        ]);
        event(new PasswordChanged($request->user()));

        return $this->ok([], 'Berhasil mengubah password');
    }

    public function requestNewVerifyEmail(Request $request)
    {
        try {
            if ($request->user()->email_verified_at) {
                return $this->error('Akun anda sudah terverifikasi', 403);
            }
            event(new RequestEmailVerification($request->user()));
            return $this->ok([], 'Kode aktivasi telah dikirim ke email Anda');
        } catch (\Throwable $th) {
            return $this->error('Gagal mengirim token' . $th->getMessage(), 500);
        }
    }

    public function verifyEmail(VerificationTokenCheckRequest $request)
    {
        try {
            if ($request->user()->email_verified_at) {
                return $this->error('Akun anda sudah terverifikasi', 403);
            }
            event(new EmailVerified($request->user()));
            return $this->ok([], 'Berhasil memverifikasi email Anda');
        } catch (\Throwable $th) {
            return $this->error('Terjadi masalah pada server', 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->ok([], 'Berhasil logout');
    }
}
