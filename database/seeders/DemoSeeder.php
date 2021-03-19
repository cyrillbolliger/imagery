<?php

namespace Database\Seeders;


use App\Group;
use App\GroupLogo;
use App\Logo;
use App\Role;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

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

        $this->seed('country');
        $this->seed('canton');
        $this->seed('local');
        Schema::enableForeignKeyConstraints();
    }

    private function seed(string $state)
    {
        $logoId  = factory(Logo::class)->state($state)->create()->id;
        $groupId = factory(Group::class)->state($state)->create()->id;

        factory(GroupLogo::class)->create([
            'logo_id'  => $logoId,
            'group_id' => $groupId,
        ]);

        $userId = factory(User::class)->state($state)->create()->id;

        factory(Role::class)->state($state)->create([
            'group_id' => $groupId,
            'user_id'  => $userId,
        ]);
    }
}
