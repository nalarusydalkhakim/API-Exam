<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\EmailCheckRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use App\Events\RequestPasswordReset;
use App\Events\PasswordReset;
use App\Events\RequestEmailVerification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();
            $status = 'active';
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => $status,
                'level' => 'user'
            ]);

            $user->profile()->create([]);

            $user->assignRole('Peserta Event');
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $this->ok($user->load('profile'));
    }

    public function login(LoginRequest $request)
    {
        try {
            $request = Request::create('oauth/token', 'POST', [
                'grant_type' => 'password',
                'username' => $request->email,
                'password' => $request->password,
                'client_id' => config('passport.password_grant_client.id'),
                'client_secret' => config('passport.password_grant_client.secret'),
                'scope' => '',
            ]);

            $req = app()->handle($request);

            $response = json_decode($req->getContent(), true);

            if ($req->isSuccessful()) {
                return $this->ok($response, 'Berhasil login');
            } else {
                $response = collect($response);
                $errorMessage = '';
                $errorCode = 500;
                $errors[] = $response->get('error_description');
                $error = $response->get('error');
                if ($error == 'invalid_request' || $error == 'invalid_grant') {
                    $errorMessage = 'Email atau password salah';
                    $errorCode = 422;
                } else if ($error == 'invalid_client') {
                    $errorMessage = 'Setting password_grant_client first!';
                }
                if (!$error) {
                    return $req;
                }
                return $this->error($errorMessage, $errorCode, $errors);
            }
        } catch (\Throwable $th) {
            return $this->error('something went wrong! ' . $th->getMessage(), 500);
        }
    }

    public function refreshToken(Request $request)
    {
        try {
            $req = Http::asForm()->post(config('app.url') . '/oauth/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $request->refresh_token,
                'client_id' => config('passport.password_grant_client.id'),
                'client_secret' => config('passport.password_grant_client.secret'),
                'scope' => '',
            ]);
            if ($req->successful()) {
                return $this->ok($req->json(), 'Berhasil memperbarui token');
            } else {
                $error = $req->json()['error'];
                $errorMessage = '';
                $errorCode = 500;
                $errors = $req->json();
                if ($error == 'invalid_request') {
                    $errorMessage = $error = $req->json()['message'];
                    $errorCode = 422;
                }
                return $this->error($errorMessage, $errorCode, $errors);
            }
        } catch (\Throwable $th) {
            return $this->error('something went wrong!', 500);
        }
    }

    public function requestPasswordReset(EmailCheckRequest $request)
    {
        try {
            $executed = RateLimiter::attempt(
                'send-email-reset:' . $request->get('email'),
                2,
                function () use ($request) {
                    $user = User::where('email', $request->get('email'))->first();
                    event(new RequestPasswordReset($user));
                }
            );

            if (!$executed) {
                return $this->error('Percobaan terlalu banyak', 422, ['email' => ['Percobaan terlalu banyak, silahkan coba kembali 1 menit kemudian']]);
            }
            return $this->ok([], 'Berhasil mengirim kode, Silahkan cek email Anda.');
        } catch (\Throwable $th) {
            return $this->error('Gagal mengirim token', 500);
        }
    }

    public function passwordReset(ResetPasswordRequest $request)
    {
        $user = User::where('email', $request->get('email'))->first();
        $user->password = Hash::make($request->password);
        $user->save();
        event(new PasswordReset($user));
        return $this->ok([], 'Password anda berhasil direset. Silahkan login kembali');
    }
}
