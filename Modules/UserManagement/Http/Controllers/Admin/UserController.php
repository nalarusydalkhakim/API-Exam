<?php

namespace Modules\UserManagement\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\UserManagement\Http\Requests\Admin\CreateUserProfileRequest;
use Modules\UserManagement\Http\Requests\Admin\CreateUserRequest;
use Modules\UserManagement\Http\Requests\Admin\UpdateUserProfileRequest;
use Modules\UserManagement\Http\Requests\Admin\UpdateUserRequest;
use Modules\UserManagement\Http\Requests\Admin\UpdateUserRoleRequest;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role_or_permission:Super Admin|Lihat Pengguna'])->only('index', 'show');
        $this->middleware(['role_or_permission:Super Admin|Tambah Pengguna'])->only('store');
        $this->middleware(['role_or_permission:Super Admin|Edit Pengguna'])->only('update');
        $this->middleware(['role_or_permission:Super Admin|Hapus Pengguna'])->only('destroy');
    }

    public function index(Request $request)
    {
        $data = User::with('profile', 'roles')
            ->when($request->has('role'), function ($q) use ($request) {
                $q->role($request->role);
            })
            ->when(!$request->user()->hasRole('Super Admin'), function ($q) {
                $q->whereIn('level', ['committee', 'user']);
            })
            ->when($request->has('level'), function ($q) use ($request) {
                $q->where('level', $request->level);
            })
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            })
            ->paginate($request->get('per_page', 10));
        return $this->ok($data, 'Data admin event');
    }

    public function store(CreateUserRequest $requestUser, CreateUserProfileRequest $requestUserProfile, UpdateUserRoleRequest $requestUserRole)
    {
        try {
            DB::beginTransaction();
            $role = Role::findByName($requestUserRole->role);
            $photo = null;
            if ($requestUser->photo) {
                $photo = $requestUser->file('photo')->store('user-photos');
            }
            $user = User::create([
                'email' => $requestUser->email,
                'name' => $requestUser->name,
                'password' => Hash::make($requestUser->password),
                'status' => 'active',
                'level' => $role->level,
                'photo' => $photo
            ]);

            $user->syncRoles([$requestUserRole->role]);

            $user->profile()->create($requestUserProfile->validated());
            DB::commit();

            return $this->ok($user, 'berhasil menyimpan data');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function show(User $user)
    {
        return $this->ok(($user->load('profile', 'roles')), 'detail data');
    }

    public function update(User $user, UpdateUserRequest $requestUser, UpdateUserProfileRequest $requestUserProfile, UpdateUserRoleRequest $requestUserRole)
    {
        try {
            DB::beginTransaction();
            $role = Role::findByName($requestUserRole->role);
            $photo = $user->getRawOriginal('photo');
            if ($requestUser->photo) {
                $photo = $requestUser->file('photo')->store('user-photos');
            }
            $user->update([
                'email' => $requestUser->email,
                'name' => $requestUser->name,
                'password' => $requestUser->password ? Hash::make($requestUser->password) : $user->password,
                'status' => 'active',
                'level' => $role->level,
                'photo' => $photo
            ]);

            if ($requestUserRole->role) {
                $user->syncRoles([$requestUserRole->role]);
            }

            $user->profile()->update($requestUserProfile->validated());
            DB::commit();

            return $this->ok($user, 'berhasil menyimpan data');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function destroy(User $user)
    {
        return $this->ok($user->delete(), 'Data dihapus');
    }
}
