<?php

namespace Modules\UserManagement\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $data = Role::get();
        return $this->ok($data, 'Data role event');
    }
}
