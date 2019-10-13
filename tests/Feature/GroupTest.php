<?php

namespace Tests\Feature;

use App\Group;
use App\Role;
use App\User;
use RootSeeder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RootSeeder::class);
    }

    public function testGetGroup__noAdmin__403()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => false,
                'group_id' => $group->id
            ])
        );

        $response = $this->actingAs($manager)
                         ->getJson("/groups/$group->id");

        $response->assertStatus(403);
    }

    public function testGetGroup__admin__200()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => true,
                'group_id' => $group->id
            ])
        );

        $response = $this->actingAs($manager)
                         ->getJson("/groups/$group->id");

        $response->assertStatus(200);
        $response->assertJson($group->toArray());
    }

    public function testGetGroup__childGroupAdmin__200()
    {
        $group = factory(Group::class)->create();
        $child = factory(Group::class)->create([
            'parent_id' => $group->id
        ]);

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => true,
                'group_id' => $group->id
            ])
        );

        $response = $this->actingAs($manager)
                         ->getJson("/groups/$child->id");

        $response->assertStatus(200);
        $response->assertJson($child->toArray());
    }
}
