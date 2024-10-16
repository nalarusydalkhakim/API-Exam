<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'admin' => [
                'Super Admin',
                'Admin Sistem',
            ],
            'committee' => [
                'Admin Event',
            ],
            'user' => [
                'Peserta Event'
            ]
        ];

        foreach ($roles as $level => $role) {
            foreach ($role as $roleName) {
                if (!Role::where('name', $roleName)->first()) {
                    $roleModel = new Role;
                    $roleModel->name = $roleName;
                    $roleModel->level = $level;
                    $roleModel->guard_name = 'api';
                    $roleModel->save();
                }
            }
        }
    }
}
