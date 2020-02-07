<?php

namespace Tests\Feature;

use App\Group;
use App\Logo;
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
                         ->getJson("/api/1/groups/$group->id");

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
                         ->getJson("/api/1/groups/$group->id");

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
                         ->getJson("/api/1/groups/$child->id");

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
                         ->getJson("/api/1/groups");

        $response->assertStatus(200);

        $response->assertJsonFragment(['id' => $root1->id]);
        $response->assertJsonFragment(['id' => $child1->id]);
        $response->assertJsonFragment(['id' => $grandchild->id]);
        $response->assertJsonFragment(['id' => $child2->id]);
        $response->assertJsonFragment(['id' => $root2->id]);

        $response->assertJsonMissing(['id' => $detached->id]);
        $response->assertJsonMissing(['id' => $useOnly->id]);
    }

    public function testGetGroups__superAdmin__200()
    {
        $group1 = factory(Group::class)->create(['name' => 'group1']);
        $group2 = factory(Group::class)->create(['name' => 'group2']);

        $manager = factory(User::class)->create(['super_admin' => true]);

        $response = $this->actingAs($manager)
                         ->getJson("/api/1/groups");

        $response->assertStatus(200);

        $response->assertJsonFragment(['id' => $group1->id])
                 ->assertJsonFragment(['id' => $group2->id]);
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
                         ->putJson("/api/1/groups/$child->id", $child->toArray());

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $child->id]);
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
                         ->putJson("/api/1/groups/$child->id", $child->toArray());

        $response->assertStatus(422);
        $this->assertNotEmpty($response->json('errors.parent_id'));
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
                         ->putJson("/api/1/groups/$child->id", $child->toArray());

        $response->assertStatus(422);
        $this->assertNotEmpty($response->json('errors.parent_id'));
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
                         ->putJson("/api/1/groups/$group->id", $group->toArray());

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
                         ->putJson("/api/1/groups/$gen1->id", $gen1->toArray());

        $response->assertStatus(422);
        $this->assertNotEmpty($response->json('errors.parent_id'));
    }

    public function testDeleteGroup__admin__204()
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
                         ->deleteJson("/api/1/groups/$group->id");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('groups', [
            'id'         => $group->id,
            'deleted_at' => null
        ]);
    }

    public function testDeleteGroup__childrenAdmin__422()
    {
        $root  = factory(Group::class)->create();
        $child = factory(Group::class)->create([
            'parent_id' => $root->id
        ]);

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => true,
                'group_id' => $root->id
            ])
        );

        $response = $this->actingAs($manager)
                         ->deleteJson("/api/1/groups/$root->id");

        $response->assertStatus(422);
        $this->assertNotEmpty($response->json('errors.children'));
        $this->assertDatabaseHas('groups', [
            'id'         => $root->id,
            'deleted_at' => null
        ]);
    }

    public function testDeleteGroup__noAdmin__403()
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
                         ->deleteJson("/api/1/groups/$group->id");

        $response->assertStatus(403);
        $this->assertDatabaseHas('groups', [
            'id'         => $group->id,
            'deleted_at' => null
        ]);
    }

    public function testPostGroup__admin__201()
    {
        $parent = factory(Group::class)->create();

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => true,
                'group_id' => $parent->id
            ])
        );

        $child = factory(Group::class)->make([
            'parent_id' => $parent->id,
            'name'      => 'inserted child',
            'added_by'  => $manager->id,
        ]);

        $response = $this->actingAs($manager)
                         ->postJson("/api/1/groups", $child->toArray());

        $response->assertStatus(201);
        $this->assertDatabaseHas('groups', [
            'name' => $child->name
        ]);
    }

    public function testPostGroup__noAdmin__403()
    {
        $parent = factory(Group::class)->create();

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => false,
                'group_id' => $parent->id,
                'added_by' => $manager->id,
            ])
        );

        $child = factory(Group::class)->make([
            'parent_id' => $parent->id,
            'name'      => 'inserted child'
        ]);

        $response = $this->actingAs($manager)
                         ->postJson("/api/1/groups", $child->toArray());

        $response->assertStatus(403);
        $this->assertDatabaseMissing('groups', [
            'name' => $child->name
        ]);
    }

    public function testPostGroup__noParentSuperAdmin__422()
    {
        $manager = factory(User::class)->create(['super_admin' => true]);

        $group = factory(Group::class)->make([
            'parent_id' => null,
            'name'      => 'no parent',
            'added_by'  => $manager->id,
        ]);

        $response = $this->actingAs($manager)
                         ->postJson("/api/1/groups", $group->toArray());

        $response->assertStatus(422);
        $this->assertNotEmpty($response->json('errors.parent_id'));
        $this->assertDatabaseMissing('groups', [
            'name' => $group->name
        ]);
    }

    public function testGetGroupsUsers__admin_200()
    {
        $root1 = factory(Group::class)->create();
        $root2 = factory(Group::class)->create();
        $child = factory(Group::class)->create(['parent_id' => $root1->id]);

        $user0 = factory(User::class)->create();
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();

        $manager = factory(User::class)->create(['super_admin' => true]);

        $user0->roles()->save(factory(Role::class)->make(['admin' => false, 'group_id' => $root1->id]));
        $user1->roles()->save(factory(Role::class)->make(['admin' => false, 'group_id' => $root1->id]));
        $user2->roles()->save(factory(Role::class)->make(['admin' => false, 'group_id' => $root2->id]));
        $user3->roles()->save(factory(Role::class)->make(['admin' => false, 'group_id' => $child->id]));

        $response = $this->actingAs($manager)
                         ->getJson("/api/1/groups/{$root1->id}/users/");

        $response->assertStatus(200);

        $response->assertJsonFragment($user0->toArray());
        $response->assertJsonFragment($user1->toArray());
        $response->assertJsonMissing(['id' => $user2->id]);
        $response->assertJsonMissing(['id' => $user3->id]);
    }

    public function testGetGroupsLogos__200()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => true,
                'group_id' => $group->id
            ])
        );

        $logo1 = factory(Logo::class)->create();
        $logo2 = factory(Logo::class)->create();
        $logo3 = factory(Logo::class)->create();
        $group->logos()->attach($logo1);
        $group->logos()->attach($logo2);

        $response = $this->actingAs($manager)
                         ->getJson("/api/1/groups/{$group->id}/logos");

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $logo1->id]);
        $response->assertJsonFragment(['id' => $logo2->id]);
        $response->assertJsonMissing(['id' => $logo3->id]);
    }
}
