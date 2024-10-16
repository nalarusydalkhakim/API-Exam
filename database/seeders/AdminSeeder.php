<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate([
            'email' => 'superadmin@mail.com'
        ], [
            'id' => Uuid::uuid4()->toString(),
            'name' => 'Super Admin',
            'password' => Hash::make('superadmin@mail.com'),
            'level' => 'admin',
            'status' => 'active'
        ]);

        DB::table('user_profiles')->updateOrInsert([
            'id' => $admin->id,
            'user_id' => $admin->id,
        ]);
        $admin->syncRoles(['Super Admin']);
    }
}
