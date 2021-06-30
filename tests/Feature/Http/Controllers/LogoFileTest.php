<?php

namespace Tests\Feature\Http\Controllers;

use App\Group;
use App\Logo;
use App\Role;
use App\User;
use Database\Seeders\RootSeeder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestStorage;

class LogoFileTest extends TestCase
{
    use RefreshDatabase;
    use TestStorage;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RootSeeder::class);
        $this->setUpTestStorage();
    }

    public function testGet__user__200()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create([
            'super_admin' => false,
            'enabled'     => true,
        ]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => false,
                'group_id' => $group->id
            ])
        );

        $logo = factory(Logo::class)->create();
        $group->logos()->attach($logo);

        $response = $this->actingAs($manager)
                         ->get("/api/1/files/logos/{$logo->id}/dark/500");

        $response->assertStatus(200);
    }

    public function testGet__user__oversize__422()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create([
            'super_admin' => false,
            'enabled'     => true,
        ]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => false,
                'group_id' => $group->id
            ])
        );

        $logo = factory(Logo::class)->create();
        $group->logos()->attach($logo);

        $response = $this->actingAs($manager)
                         ->get("/api/1/files/logos/{$logo->id}/dark/5001");

        $response->assertStatus(422);
    }

    public function testGet__user__404()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create([
            'super_admin' => false,
            'enabled'     => true,
        ]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => false,
                'group_id' => $group->id
            ])
        );

        $logo = factory(Logo::class)->create(['type' => '404']);
        $group->logos()->attach($logo);

        $response = $this->actingAs($manager)
                         ->get("/api/1/files/logos/{$logo->id}/light/500");

        $response->assertStatus(404);
    }

    public function testGet__nonAttachedUser__403()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create([
            'super_admin' => false,
            'enabled'     => true,
        ]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => false,
                'group_id' => $group->id
            ])
        );

        $logo = factory(Logo::class)->create();

        $response = $this->actingAs($manager)
                         ->get("/api/1/files/logos/{$logo->id}/light/500");

        $response->assertStatus(403);
    }
}
