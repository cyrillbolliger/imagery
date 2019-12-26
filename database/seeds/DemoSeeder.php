<?php

use App\Role;
use App\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
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
        factory(User::class)->state('superAdmin')->create();

        factory(Role::class)->state('countryAdmin')->create();
        factory(Role::class)->state('cantonAdmin')->create();
        factory(Role::class)->state('localUser')->create();
        Schema::enableForeignKeyConstraints();
    }
}
