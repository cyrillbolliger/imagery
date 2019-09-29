<?php

use App\Group;
use App\Role;
use App\User;
use Illuminate\Database\Seeder;

class RootUserGroupRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // there are circular dependencies on users, groups and logos
        Schema::disableForeignKeyConstraints();
        factory(User::class)->state('root')->create();
        Schema::enableForeignKeyConstraints();

        factory(Group::class)->state('root')->create();
        factory(Role::class)->state('root')->create();
    }
}
