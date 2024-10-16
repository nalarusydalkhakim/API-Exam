<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = array(
            [
                'name' => 'Sistem Event',
                'features' => array(
                    array(
                        'name' => 'Data Event',
                        'permissions' => array(
                            array(
                                'roles' => array(
                                    'Super Admin',
                                    'Admin Sistem'
                                ),
                                'actions' => array(
                                    'Lihat',
                                    'Tambah',
                                    'Edit',
                                    'Hapus'
                                )
                            ),
                            array(
                                'roles' => array(
                                    'Admin Event'
                                ),
                                'actions' => array(
                                    'Lihat',
                                    'Edit'
                                )
                            )
                        )
                    ),
                    array(
                        'name' => 'Panitia Event',
                        'permissions' => array(
                            array(
                                'roles' => array(
                                    'Super Admin',
                                    'Admin Sistem'
                                ),
                                'actions' => array(
                                    'Lihat',
                                    'Tambah',
                                    'Edit',
                                    'Hapus'
                                )
                            )
                        )
                    ),
                    array(
                        'name' => 'Peserta Event',
                        'permissions' => array(
                            array(
                                'roles' => array(
                                    'Super Admin',
                                    'Admin Sistem'
                                ),
                                'actions' => array(
                                    'Lihat',
                                    'Tambah',
                                    'Edit',
                                    'Hapus'
                                )
                            )
                        )
                    ),
                    array(
                        'name' => 'Sponsor Event',
                        'permissions' => array(
                            array(
                                'roles' => array(
                                    'Super Admin',
                                    'Admin Sistem',
                                    'Admin Event'
                                ),
                                'actions' => array(
                                    'Lihat',
                                    'Tambah',
                                    'Edit',
                                    'Hapus'
                                )
                            )
                        )
                    ),
                    array(
                        'name' => 'Data Pembayaran',
                        'permissions' => array(
                            array(
                                'roles' => array(
                                    'Super Admin',
                                    'Admin Sistem'
                                ),
                                'actions' => array(
                                    'Lihat',
                                    'Tambah',
                                    'Edit',
                                    'Hapus'
                                )
                            )
                        )
                    ),
                    array(
                        'name' => 'Banner',
                        'permissions' => array(
                            array(
                                'roles' => array(
                                    'Super Admin',
                                    'Admin Sistem'
                                ),
                                'actions' => array(
                                    'Lihat',
                                    'Tambah',
                                    'Edit',
                                    'Hapus'
                                )
                            )
                        )
                    ),
                )
            ],
            [
                'name' => 'Sistem Manajemen Pengguna',
                'features' => array(
                    array(
                        'name' => 'Pengguna',
                        'permissions' => array(
                            array(
                                'roles' => array(
                                    'Super Admin',
                                    'Admin Sistem'
                                ),
                                'actions' => array(
                                    'Lihat',
                                    'Tambah',
                                    'Edit',
                                    'Hapus'
                                )
                            ),
                            array(
                                'roles' => array(
                                    'Admin Event'
                                ),
                                'actions' => array(
                                    'Lihat'
                                )
                            )
                        )
                    ),
                )
            ],
            [
                'name' => 'Sistem LMS',
                'features' => array(
                    array(
                        'name' => 'Mata Pelajaran',
                        'permissions' => array(
                            array(
                                'roles' => array(
                                    'Super Admin',
                                    'Admin Sistem'
                                ),
                                'actions' => array(
                                    'Lihat',
                                    'Tambah',
                                    'Edit',
                                    'Hapus'
                                )
                            ),
                            array(
                                'roles' => array(
                                    'Admin Event'
                                ),
                                'actions' => array(
                                    'Lihat'
                                )
                            )
                        )
                    ),
                    array(
                        'name' => 'Ujian Event',
                        'permissions' => array(
                            array(
                                'roles' => array(
                                    'Super Admin',
                                    'Admin Sistem',
                                    'Admin Event'
                                ),
                                'actions' => array(
                                    'Lihat',
                                    'Tambah',
                                    'Edit',
                                    'Hapus'
                                )
                            )
                        )
                    ),
                    array(
                        'name' => 'Bank Ujian',
                        'permissions' => array(
                            array(
                                'roles' => array(
                                    'Admin Event'
                                ),
                                'actions' => array(
                                    'Lihat',
                                    'Tambah',
                                    'Edit',
                                    'Hapus'
                                )
                            ),
                            array(
                                'roles' => array(
                                    'Super Admin',
                                    'Admin Sistem'
                                ),
                                'actions' => array(
                                    'Lihat',
                                    'Edit',
                                    'Hapus'
                                )
                            )
                        )
                    ),
                    array(
                        'name' => 'Bank Soal',
                        'permissions' => array(
                            array(
                                'roles' => array(
                                    'Admin Event'
                                ),
                                'actions' => array(
                                    'Lihat',
                                    'Tambah',
                                    'Edit',
                                    'Hapus'
                                )
                            ),
                            array(
                                'roles' => array(
                                    'Super Admin',
                                    'Admin Sistem'
                                ),
                                'actions' => array(
                                    'Lihat',
                                    'Edit',
                                    'Hapus'
                                )
                            ),
                        )
                    )
                )
            ],
        );

        foreach ($modules as $module) {
            foreach ($module['features'] as $feature) {
                foreach ($feature['permissions'] as $permission) {
                    foreach ($permission['roles'] as $role) {
                        $roleModel = Role::where('name', $role)->where('guard_name', 'api')->first();
                        foreach ($permission['actions'] as $action) {
                            $TambahdPermission = Permission::firstOrCreate([
                                'name' => $action . ' ' . $feature['name'],
                                'guard_name' => 'api'
                            ]);
                            $roleModel->givePermissionTo($TambahdPermission->name);
                        }
                    }
                }
            }
        }
    }
}
