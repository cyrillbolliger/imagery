<?php

namespace Database\Seeders;


use App\Group;
use App\Role;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RootSeeder extends Seeder
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
        factory(Group::class)->state('root')->create();
        factory(Role::class)->state('root')->create();
        Schema::enableForeignKeyConstraints();
    }
}
