<?php

namespace Tests\Feature;

use App\Group;
use App\Logo;
use App\Role;
use App\User;
use RootSeeder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoFileTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RootSeeder::class);
    }

    public function testGet__user__200()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => false,
                'group_id' => $group->id
            ])
        );

        $logo = factory(Logo::class)->create();
        $group->logos()->attach($logo);

        $response = $this->actingAs($manager)
                         ->get("/api/1/files/logos/$logo->id");

        $response->assertStatus(200);
    }

    public function testGet__user__404()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => false,
                'group_id' => $group->id
            ])
        );

        $logo = factory(Logo::class)->create(['type' => '404']);
        $group->logos()->attach($logo);

        $response = $this->actingAs($manager)
                         ->get("/api/1/files/logos/$logo->id/white");

        $response->assertStatus(404);
    }

    public function testGet__nonAttachedUser__403()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => false,
                'group_id' => $group->id
            ])
        );

        $logo = factory(Logo::class)->create();

        $response = $this->actingAs($manager)
                         ->get("/api/1/files/logos/$logo->id/white");

        $response->assertStatus(403);
    }
}
