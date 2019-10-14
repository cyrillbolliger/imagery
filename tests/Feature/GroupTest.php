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

    public function testGetGroups__admin__200()
    {
        $detached   = factory(Group::class)->create(['name' => 'detached']);
        $useOnly    = factory(Group::class)->create(['name' => 'useOnly']);
        $root1      = factory(Group::class)->create(['name' => 'root1']);
        $root2      = factory(Group::class)->create(['name' => 'root2']);
        $child1     = factory(Group::class)->create([
            'parent_id' => $root1->id,
            'name'      => 'child1'
        ]);
        $child2     = factory(Group::class)->create([
            'parent_id' => $root1->id,
            'name'      => 'child2'
        ]);
        $grandchild = factory(Group::class)->create([
            'parent_id' => $child1->id,
            'name'      => 'grandchild'
        ]);

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => true,
                'group_id' => $root1->id
            ])
        );
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => true,
                'group_id' => $root2->id
            ])
        );
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => false,
                'group_id' => $useOnly->id
            ])
        );

        $response = $this->actingAs($manager)
                         ->getJson("/groups");

        $response->assertStatus(200);

        $response->assertJsonPath('0.id', $root1->id);
        $response->assertJsonPath('0.descendants.0.id', $child1->id);
        $response->assertJsonPath('0.descendants.0.descendants.0.id', $grandchild->id);
        $response->assertJsonPath('0.descendants.1.id', $child2->id);
        $response->assertJsonPath('1.id', $root2->id);

        $response->assertJsonMissing(['id' => $detached->id]);
        $response->assertJsonMissing(['id' => $useOnly->id]);
    }

    public function testPutGroup__admin__200()
    {
        $root1 = factory(Group::class)->create();
        $root2 = factory(Group::class)->create();
        $child = factory(Group::class)->create([
            'parent_id' => $root1->id
        ]);

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => true,
                'group_id' => $root1->id
            ])
        );
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => true,
                'group_id' => $root2->id
            ])
        );

        $child->name      = 'changed';
        $child->parent_id = $root2->id;

        $response = $this->actingAs($manager)
                         ->putJson("/groups/$child->id", $child->toArray());

        $response->assertStatus(200);
        $response->assertJson($child->toArray());
    }

    public function testPutGroup__notParentAdmin__422()
    {
        $root1 = factory(Group::class)->create();
        $root2 = factory(Group::class)->create();
        $child = factory(Group::class)->create([
            'parent_id' => $root1->id
        ]);

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => true,
                'group_id' => $child->id
            ])
        );
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => true,
                'group_id' => $root2->id
            ])
        );

        $child->name      = 'changed';
        $child->parent_id = $root2->id;

        $response = $this->actingAs($manager)
                         ->putJson("/groups/$child->id", $child->toArray());

        $response->assertStatus(422);
    }

    public function testPutGroup__notParentAdminAdmin__422()
    {
        $root1 = factory(Group::class)->create();
        $root2 = factory(Group::class)->create();
        $child = factory(Group::class)->create([
            'parent_id' => $root1->id
        ]);

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => true,
                'group_id' => $root1->id
            ])
        );
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => false,
                'group_id' => $root2->id
            ])
        );

        $child->name      = 'changed';
        $child->parent_id = $root2->id;

        $response = $this->actingAs($manager)
                         ->putJson("/groups/$child->id", $child->toArray());

        $response->assertStatus(422);
    }

    public function testPutGroup__noAdmin__403()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => false,
                'group_id' => $group->id
            ])
        );

        $group->name = 'changed';

        $response = $this->actingAs($manager)
                         ->putJson("/groups/$group->id", $group->toArray());

        $response->assertStatus(403);
    }

    public function testPutGroup__deconnectSuperAdmin__422()
    {
        $root = factory(Group::class)->create([
            'name' => 'root',
        ]);
        $gen1 = factory(Group::class)->create([
            'parent_id' => $root->id,
            'name'      => 'gen 1'
        ]);
        $gen2 = factory(Group::class)->create([
            'parent_id' => $gen1->id,
            'name'      => 'gen 2'
        ]);
        $gen3 = factory(Group::class)->create([
            'parent_id' => $gen2->id,
            'name'      => 'gen 3'
        ]);

        $manager = factory(User::class)->create(['super_admin' => true]);

        $gen1->name      = 'changed';
        $gen1->parent_id = $gen3->id;

        $response = $this->actingAs($manager)
                         ->putJson("/groups/$gen1->id", $gen1->toArray());

        $response->assertStatus(422);
    }
}
