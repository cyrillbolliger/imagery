<?php

namespace Tests\Feature;

use App\Group;
use App\Role;
use App\User;
use Illuminate\Support\Carbon;
use RootSeeder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RootSeeder::class);
    }

    public function testGetUser__strangerNoAdmin_403()
    {
        $manager = factory(User::class)->create(['super_admin' => false]);
        $managed = User::first();

        $response = $this->actingAs($manager)
                         ->getJson('/users/'.$managed->id);

        $response->assertStatus(403);
    }

    public function testGetUser__selfNoAdmin_200()
    {
        $manager = factory(User::class)->create(['super_admin' => false]);
        $managed = $manager;

        $response = $this->actingAs($manager)
                         ->getJson('/users/'.$managed->id);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id',
                     'first_name',
                     'last_name',
                     'email',
                     'added_by',
                     'managed_by',
                     'default_logo',
                     'super_admin',
                     'lang',
                     'login_count',
                     'last_login',
                     'created_at',
                     'updated_at',
                 ]);

        $this->assertNull($response->json('password'));
    }

    public function testGetUser__strangerSuperAdmin_200()
    {
        $manager = factory(User::class)->create(['super_admin' => true]);
        $managed = factory(User::class)->create();

        $response = $this->actingAs($manager)
                         ->getJson('/users/'.$managed->id);

        $response->assertStatus(200);
    }

    public function testGetUser__managedGroupAdmin_200()
    {
        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(factory(Role::class)->make(['admin' => true]));

        $managed = factory(User::class)->create([
            'managed_by' => $manager->roles()->first()->group->id
        ]);

        $response = $this->actingAs($manager)
                         ->getJson('/users/'.$managed->id);

        $response->assertStatus(200);
    }

    public function testGetUser__managedParentAdmin_200()
    {
        $parentGroup = factory(Group::class)->create(['name' => 'parent']);
        $childGroup  = factory(Group::class)->create(['name' => 'child', 'parent_id' => $parentGroup->id]);

        $role = factory(Role::class)->make(['admin' => true, 'group_id' => $parentGroup->id]);

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save($role);

        $managed = factory(User::class)->create([
            'managed_by' => $childGroup->id
        ]);

        $response = $this->actingAs($manager)
                         ->getJson('/users/'.$managed->id);

        $response->assertStatus(200);
    }

    public function testGetUsers__nonAdmin_403()
    {
        $user = factory(User::class)->create(['super_admin' => false]);

        $response = $this->actingAs($user)
                         ->getJson('/users/');

        $response->assertStatus(403);
    }

    public function testGetUsers__superAdmin_200()
    {
        $user  = factory(User::class)->create(['super_admin' => true]);
        $users = User::all();

        $response = $this->actingAs($user)
                         ->getJson('/users/');

        $response->assertStatus(200);
        $response->assertJson($users->toArray());
    }

    public function testGetUsers__admin_200()
    {
        $root1      = factory(Group::class)->create();
        $root2      = factory(Group::class)->create();
        $child      = factory(Group::class)->create(['parent_id' => $root1->id]);
        $grandchild = factory(Group::class)->create(['parent_id' => $child->id]);

        $user1        = $root1->users()->save(factory(User::class)->make());
        $user2        = $root2->users()->save(factory(User::class)->make());
        $user3        = $child->users()->save(factory(User::class)->make());
        $user4        = $grandchild->users()->save(factory(User::class)->make());
        $detachedUser = factory(User::class)->create();

        $manager      = factory(User::class)->create();
        $roleNonAdmin = $manager->roles()->save(factory(Role::class)->make(['admin' => false, 'group_id' => $root1]));
        $roleAdmin1   = $manager->roles()->save(factory(Role::class)->make(['admin' => true, 'group_id' => $child]));
        $roleAdmin2   = $manager->roles()->save(factory(Role::class)->make(['admin' => true, 'group_id' => $root2]));

        $response = $this->actingAs($manager)
                         ->getJson('/users/');

        $response->assertStatus(200);

        $response->assertJsonFragment($user2->toArray());
        $response->assertJsonFragment($user3->toArray());
        $response->assertJsonFragment($user4->toArray());
        $response->assertJsonMissing(['id' => $user1->id]);
        $response->assertJsonMissing(['id' => $detachedUser->id]);
    }

    public function testPutUser__superAdmin_200()
    {
        $manager = factory(User::class)->create(['super_admin' => true]);
        $managed = factory(User::class)->create(['super_admin' => false]);

        $managed->super_admin = true;
        sleep(1); // so we can test the modified time

        $response = $this->actingAs($manager)
                         ->putJson('/users/'.$managed->id, $managed->toArray());

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id'          => $managed->id,
            'super_admin' => true,
        ]);

        $updated = new Carbon($response->json('updated_at'));
        $this->assertTrue($managed->created_at->lessThan($updated));
    }

    public function testPutUser__admin_200()
    {
        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(factory(Role::class)->make(['admin' => true]));

        $managed = factory(User::class)->create([
            'managed_by' => $manager->roles()->first()->group->id
        ]);

        $managed->first_name = 'Changed';

        $response = $this->actingAs($manager)
                         ->putJson('/users/'.$managed->id, $managed->toArray());

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id'         => $managed->id,
            'first_name' => $managed->first_name,
        ]);
    }

    public function testPutUser__adminMakeSuperAdmin_422()
    {
        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(factory(Role::class)->make(['admin' => true]));

        $managed = factory(User::class)->create([
            'managed_by' => $manager->roles()->first()->group->id
        ]);

        $managed->super_admin = true;

        $response = $this->actingAs($manager)
                         ->putJson('/users/'.$managed->id, $managed->toArray());

        $response->assertStatus(422);
    }

    public function testPutUser__adminRemoveSuperAdmin_422()
    {
        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(factory(Role::class)->make(['admin' => true]));

        $managed = factory(User::class)->create([
            'managed_by'  => $manager->roles()->first()->group->id,
            'super_admin' => true,
        ]);

        $managed->super_admin = false;

        $response = $this->actingAs($manager)
                         ->putJson('/users/'.$managed->id, $managed->toArray());

        $response->assertStatus(422);
    }

    public function testPutUser__adminExistingSuperAdmin_200()
    {
        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(factory(Role::class)->make(['admin' => true]));

        $managed = factory(User::class)->create([
            'managed_by'  => $manager->roles()->first()->group->id,
            'super_admin' => true,
        ]);

        $managed->last_name = 'Super Admin';

        $response = $this->actingAs($manager)
                         ->putJson('/users/'.$managed->id, $managed->toArray());

        $response->assertStatus(200);
    }

    public function testPutUser__weakPassword_422()
    {
        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(factory(Role::class)->make(['admin' => true]));

        $managed = factory(User::class)->create([
            'managed_by' => $manager->roles()->first()->group->id
        ]);

        $response = $this->actingAs($manager)
                         ->putJson('/users/'.$managed->id, [
                             'id'       => $managed->id,
                             'password' => 'weak' // we can't set this using the toArray method
                         ]);

        $response->assertStatus(422);
    }

    public function testPutUser__strongPassword_200()
    {
        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(factory(Role::class)->make(['admin' => true]));

        $managed = factory(User::class)->create([
            'managed_by' => $manager->roles()->first()->group->id
        ]);

        $data             = $managed->toArray();
        $data['password'] = 'oq/7Ea5$'; // we can't set this using the toArray method

        $response = $this->actingAs($manager)
                         ->putJson('/users/'.$managed->id, $data);

        $response->assertStatus(200);
    }

    public function testDeleteUser__204()
    {
        $manager = factory(User::class)->create(['super_admin' => false]);
        $managed = $manager;

        $response = $this->actingAs($manager)
                         ->deleteJson('/users/'.$managed->id);

        $response->assertStatus(204);
        $this->assertNull(User::find($managed->id));
        $this->assertInstanceOf(User::class, User::withTrashed()->find($managed->id));
    }

    public function testPostUser__strangerSuperAdmin_201()
    {
        $manager = factory(User::class)->create(['super_admin' => true]);
        $managed = factory(User::class)->make();

        $data             = $managed->toArray();
        $data['password'] = 'oq/7Ea5$'; // we can't set this using the toArray method

        $response = $this->actingAs($manager)
                         ->postJson('/users', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => $managed->email,
        ]);
    }

    public function testPostUser__strangerAdmin_201()
    {
        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(factory(Role::class)->make(['admin' => true]));

        $managed = factory(User::class)->make();

        $data             = $managed->toArray();
        $data['password'] = 'oq/7Ea5$'; // we can't set this using the toArray method

        $response = $this->actingAs($manager)
                         ->postJson('/users', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => $managed->email,
        ]);
    }

    public function testPostUser__superAdminAdmin_422()
    {
        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(factory(Role::class)->make(['admin' => true]));

        $managed = factory(User::class)->make(['super_admin' => true]);

        $data             = $managed->toArray();
        $data['password'] = 'oq/7Ea5$'; // we can't set this using the toArray method

        $response = $this->actingAs($manager)
                         ->postJson('/users', $data);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', [
            'email' => $managed->email,
        ]);
    }


    /**
     * todo:
     * - validate managed by
     * - validate default_logo
     */
}
