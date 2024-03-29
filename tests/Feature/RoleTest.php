<?php

namespace Tests\Feature;

use App\Group;
use App\Role;
use App\User;
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
        $manager = factory(User::class)->create([
            'super_admin' => false,
            'enabled'     => true,
        ]);
        $manager->roles()->save(factory(Role::class)->make(['admin' => false]));
        $managed = factory(User::class)->create(['super_admin' => false]);

        $response = $this->actingAs($manager)
                         ->getJson("/api/1/users/$managed->id/roles");

        $response->assertStatus(403);
    }

    public function testGetRoles__admin__200()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create([
            'super_admin' => false,
            'enabled'     => true,
        ]);
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
                         ->getJson("/api/1/users/$managed->id/roles");

        $response->assertStatus(200);
        $response->assertJson($managed->roles->toArray());
    }

    public function testGetRole__admin__200()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create([
            'super_admin' => false,
            'enabled'     => true,
        ]);
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
                         ->getJson("/api/1/users/$managed->id/roles/$role->id");

        $response->assertStatus(200);

        $response->assertJson($managed->roles()->with('group')->get()->toArray()[0]);
    }

    public function testGetRole__adminNotAttachedToUser__404()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create([
            'super_admin' => false,
            'enabled'     => true,
        ]);
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
                         ->getJson("/api/1/users/$managed->id/roles/$role->id");

        $response->assertStatus(404);
    }

    public function testGetRole__noAdmin__403()
    {
        $manager = factory(User::class)->create([
            'super_admin' => false,
            'enabled'     => true,
        ]);

        $managed = factory(User::class)->create();
        $role    = factory(Role::class)->create([
            'user_id' => $managed->id
        ]);

        $response = $this->actingAs($manager)
                         ->getJson("/api/1/users/$managed->id/roles/$role->id");

        $response->assertStatus(403);
    }

    public function testPutRole__userChangeAdmin__422()
    {
        $manager = factory(User::class)->create([
            'super_admin' => false,
            'enabled'     => true,
        ]);
        $group   = factory(Group::class)->create();
        factory(Role::class)->create([
            'group_id' => $group->id,
            'user_id'  => $manager->id,
            'admin'    => true,
        ]);

        $managedOld = factory(User::class)->create(['managed_by' => $group->id]);
        $managedNew = factory(User::class)->create(['managed_by' => $group->id]);

        $role = factory(Role::class)->create([
            'user_id'  => $managedOld->id,
            'group_id' => $group->id
        ]);

        $role->user_id = $managedNew->id;

        $response = $this->actingAs($manager)
                         ->putJson("/api/1/users/$managedOld->id/roles/$role->id", $role->toArray());

        $response->assertStatus(422);
        $this->assertNotEmpty($response->json('errors.user_id'));
    }

    public function testPutRole__notOldGroupAdmin__403()
    {
        $manager = factory(User::class)->create([
            'super_admin' => false,
            'enabled'     => true,
        ]);
        $group   = factory(Group::class)->create();
        factory(Role::class)->create([
            'group_id' => $group->id,
            'user_id'  => $manager->id,
            'admin'    => true,
        ]);

        $managed = factory(User::class)->create();

        $role = factory(Role::class)->create([
            'user_id' => $managed->id,
        ]);

        $role->group_id = $group->id;

        $response = $this->actingAs($manager)
                         ->putJson("/api/1/users/$managed->id/roles/$role->id", $role->toArray());

        $response->assertStatus(403);
    }

    public function testPutRole__notNewGroupAdmin__403()
    {
        $manager = factory(User::class)->create([
            'super_admin' => false,
            'enabled'     => true,
        ]);
        $group   = factory(Group::class)->create();
        factory(Role::class)->create([
            'group_id' => $group->id,
            'user_id'  => $manager->id,
            'admin'    => true,
        ]);

        $managed = factory(User::class)->create();

        $role = factory(Role::class)->create([
            'user_id'  => $managed->id,
            'group_id' => $group->id,
        ]);

        $role->group_id = factory(Group::class)->create()->id;

        $response = $this->actingAs($manager)
                         ->putJson("/api/1/users/$managed->id/roles/$role->id", $role->toArray());

        $response->assertStatus(403);
    }

    public function testPutRole__admin__200()
    {
        $manager  = factory(User::class)->create([
            'super_admin' => false,
            'enabled'     => true,
        ]);
        $groupOld = factory(Group::class)->create();
        factory(Role::class)->create([
            'group_id' => $groupOld->id,
            'user_id'  => $manager->id,
            'admin'    => true,
        ]);
        $groupNew = factory(Group::class)->create();
        factory(Role::class)->create([
            'group_id' => $groupNew->id,
            'user_id'  => $manager->id,
            'admin'    => true,
        ]);

        $managed = factory(User::class)->create([
            'managed_by' => $groupOld->id
        ]);

        $role = factory(Role::class)->create([
            'user_id'  => $managed->id,
            'group_id' => $groupOld->id,
        ]);

        $role->group_id = $groupNew->id;
        $role->admin    = true;

        $response = $this->actingAs($manager)
                         ->putJson("/api/1/users/$managed->id/roles/$role->id", $role->toArray());

        $response->assertStatus(200);
    }

    public function testDeleteRole__admin__204()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create([
            'super_admin' => false,
            'enabled'     => true,
        ]);
        $manager->roles()->save(factory(Role::class)->make([
            'admin'    => true,
            'group_id' => $group->id
        ]));

        $role    = factory(Role::class)->make([
            'group_id' => $group->id
        ]);
        $managed = factory(User::class)->create([
            'super_admin' => false,
            'managed_by'  => $group->id
        ]);
        $managed->roles()->save($role);

        $response = $this->actingAs($manager)
                         ->deleteJson("/api/1/users/$managed->id/roles/$role->id");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('roles', [
            'id'         => $role->id,
            'deleted_at' => null
        ]);
    }

    public function testDeleteRole__adminCantManageUser__403()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create([
            'super_admin' => false,
            'enabled'     => true,
        ]);
        $manager->roles()->save(factory(Role::class)->make([
            'admin'    => true,
            'group_id' => $group->id
        ]));

        $role    = factory(Role::class)->make([
            'group_id' => $group->id
        ]);
        $managed = factory(User::class)->create([
            'super_admin' => false,
        ]);
        $managed->roles()->save($role);

        $response = $this->actingAs($manager)
                         ->deleteJson("/api/1/users/$managed->id/roles/$role->id");

        $response->assertStatus(403);
        $this->assertDatabaseHas('roles', [
            'id'         => $role->id,
            'deleted_at' => null
        ]);
    }

    public function testDeleteRole__adminCantManageGroup__403()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create([
            'super_admin' => false,
            'enabled'     => true,
        ]);
        $manager->roles()->save(factory(Role::class)->make([
            'admin'    => true,
            'group_id' => $group->id
        ]));

        $role    = factory(Role::class)->make([
            'group_id' => factory(Group::class)->create()->id
        ]);
        $managed = factory(User::class)->create([
            'super_admin' => false,
            'managed_by'  => $group->id
        ]);
        $managed->roles()->save($role);

        $response = $this->actingAs($manager)
                         ->deleteJson("/api/1/users/$managed->id/roles/$role->id");

        $response->assertStatus(403);
        $this->assertDatabaseHas('roles', [
            'id'         => $role->id,
            'deleted_at' => null
        ]);
    }

    public function testPostRole__admin__201()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create([
            'super_admin' => false,
            'enabled'     => true,
        ]);
        $manager->roles()->save(factory(Role::class)->make([
            'admin'    => true,
            'group_id' => $group->id
        ]));

        $managed = factory(User::class)->create([
            'super_admin' => false,
            'managed_by'  => $group->id
        ]);
        $role    = factory(Role::class)->make([
            'group_id' => $group->id,
            'user_id'  => $managed->id,
            'added_by' => $manager->id,
        ]);

        $response = $this->actingAs($manager)
                         ->postJson("/api/1/users/$managed->id/roles/$role->id", $role->toArray());

        $response->assertStatus(201);
        $this->assertDatabaseHas('roles', [
            'user_id'  => $managed->id,
            'group_id' => $group->id,
        ]);
    }

    public function testPostRole__adminCantManageUser__403()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create([
            'super_admin' => false,
            'enabled'     => true,
        ]);
        $manager->roles()->save(factory(Role::class)->make([
            'admin'    => true,
            'group_id' => $group->id
        ]));

        $managed = factory(User::class)->create([
            'super_admin' => false,
        ]);
        $role    = factory(Role::class)->make([
            'group_id' => $group->id,
            'user_id'  => $managed->id,
            'added_by' => $manager->id,
        ]);

        $response = $this->actingAs($manager)
                         ->postJson("/api/1/users/$managed->id/roles/$role->id", $role->toArray());

        $response->assertStatus(403);
        $this->assertDatabaseMissing('roles', [
            'user_id'  => $managed->id,
            'group_id' => $group->id,
        ]);
    }

    public function testPostRole__adminCantManageGroup__403()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create([
            'super_admin' => false,
            'enabled'     => true,
        ]);
        $manager->roles()->save(factory(Role::class)->make([
            'admin'    => true,
            'group_id' => $group->id
        ]));

        $managed = factory(User::class)->create([
            'super_admin' => false,
            'managed_by'  => $group->id
        ]);
        $role    = factory(Role::class)->make([
            'group_id' => factory(Group::class)->create()->id,
            'user_id'  => $managed->id,
            'added_by' => $manager->id,
        ]);

        $response = $this->actingAs($manager)
                         ->postJson("/api/1/users/$managed->id/roles/$role->id", $role->toArray());

        $response->assertStatus(403);
        $this->assertDatabaseMissing('roles', [
            'user_id'  => $managed->id,
            'group_id' => $group->id,
        ]);
    }
}
