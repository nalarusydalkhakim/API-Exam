<?php

namespace Modules\UserManagement\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $data = Role::query()
            ->when(!$request->user()->hasRole('Super Admin'), function ($q) {
                $q->whereIn('level', ['committee', 'user']);
            })->get();


        return $this->ok($data, 'Data role event');
    }
}
