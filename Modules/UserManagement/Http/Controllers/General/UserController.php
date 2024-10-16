<?php

namespace Modules\UserManagement\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role_or_permission:Super Admin|Lihat Pengguna'])->only('index');
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

    public function show(User $user)
    {
        return $this->ok(($user->load('profile', 'roles')), 'detail data');
    }
}
