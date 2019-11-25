<?php

use Illuminate\Database\Seeder;
use \Spatie\Permission\Models\Role;
use App\User;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;
        $user->name = 'shabeeb';
        $user->email = 'shabeeboali@gmail.com';
        $user->password = bcrypt('kinassery');
        $user->save();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $role = Role::create(['name' => 'Super Admin','guard_name'=>'api']);
        $user->assignRole($role);
    }
}
