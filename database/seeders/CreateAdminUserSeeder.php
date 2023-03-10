<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin@123')
        ]);

        $role = Role::create(['name' => 'Admin']);
        $role_user = Role::create(['name' => 'User']);

        $permissions = Permission::pluck('id','id')->all();
        $user_permissions = Permission::find(1);

        $role->syncPermissions($permissions);
        $role_user->syncPermissions($user_permissions);

        $user->assignRole([$role->id]);
    }
}
