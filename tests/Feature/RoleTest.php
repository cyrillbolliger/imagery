<?php

namespace Tests\Feature;

use App\Group;
use App\Logo;
use App\Role;
use App\User;
use Illuminate\Support\Carbon;
use RootSeeder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RootSeeder::class);
    }

    public function testGetRoles__noAdmin__403()
    {
        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(factory(Role::class)->make(['admin' => false]));
        $managed = factory(User::class)->create(['super_admin' => false]);

        $response = $this->actingAs($manager)
                         ->getJson("/users/$managed->id/roles");

        $response->assertStatus(403);
    }

    public function testGetRoles__admin__200()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(factory(Role::class)->make([
            'admin'    => true,
            'group_id' => $group->id
        ]));

        $managed = factory(User::class)->create([
            'super_admin' => false,
            'managed_by'  => $group->id
        ]);
        $managed->roles()->save(factory(Role::class)->make(['admin' => true]));
        $managed->roles()->save(factory(Role::class)->make(['admin' => false]));

        $response = $this->actingAs($manager)
                         ->getJson("/users/$managed->id/roles");

        $response->assertStatus(200);
        $response->assertJson($managed->roles->toArray());
    }

    public function testGetRole__admin__200()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(factory(Role::class)->make([
            'admin'    => true,
            'group_id' => $group->id
        ]));

        $role    = factory(Role::class)->make();
        $managed = factory(User::class)->create([
            'super_admin' => false,
            'managed_by'  => $group->id
        ]);
        $managed->roles()->save($role);
        $managed->roles()->save(factory(Role::class)->make()); // should not be part of the results

        $response = $this->actingAs($manager)
                         ->getJson("/users/$managed->id/roles/$role->id");

        $response->assertStatus(200);

        $response->assertJson($managed->roles()->with('group')->get()->toArray()[0]);
    }

    public function testGetRole__adminNotAttachedToUser__404()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(factory(Role::class)->make([
            'admin'    => true,
            'group_id' => $group->id
        ]));

        $role    = factory(Role::class)->create();
        $managed = factory(User::class)->create([
            'super_admin' => false,
            'managed_by'  => $group->id
        ]);

        $response = $this->actingAs($manager)
                         ->getJson("/users/$managed->id/roles/$role->id");

        $response->assertStatus(404);
    }

    public function testGetRole__noAdmin__403()
    {
        $manager = factory(User::class)->create(['super_admin' => false]);

        $managed = factory(User::class)->create();
        $role    = factory(Role::class)->create([
            'user_id' => $managed->id
        ]);

        $response = $this->actingAs($manager)
                         ->getJson("/users/$managed->id/roles/$role->id");

        $response->assertStatus(403);
    }
}
