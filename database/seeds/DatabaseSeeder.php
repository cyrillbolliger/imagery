<?php

use App\Group;
use App\GroupLogo;
use App\Image;
use App\Logo;
use App\Role;
use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        factory(User::class, 3)->create();
        factory(Group::class)->create();
        factory(Role::class)->create();
        factory(Logo::class)->create();
        factory(GroupLogo::class)->create();
        factory(Image::class, 10)->create();
    }
}
