<?php

namespace Tests\Feature;

use App\Group;
use App\Image;
use App\Role;
use App\User;
use RootSeeder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
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
                         ->get('/users/'.$managed->id);

        $response->assertStatus(403);
    }

    public function testGetUser__selfNoAdmin_200()
    {
        $manager = factory(User::class)->create(['super_admin' => false]);
        $managed = $manager;

        $response = $this->actingAs($manager)
                         ->get('/users/'.$managed->id);

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
    }

    public function testGetUser__strangerSuperAdmin_200()
    {
        $manager = factory(User::class)->create(['super_admin' => true]);
        $managed = factory(User::class)->create();

        $response = $this->actingAs($manager)
                         ->get('/users/'.$managed->id);

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
                         ->get('/users/'.$managed->id);

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
                         ->get('/users/'.$managed->id);

        $response->assertStatus(200);
    }

    public function testGetUsers__nonAdmin_403()
    {
        $user = factory(User::class)->create(['super_admin' => false]);

        $response = $this->actingAs($user)
                         ->get('/users/');

        $response->assertStatus(403);
    }

    public function testGetUsers__superAdmin_200()
    {
        $user  = factory(User::class)->create(['super_admin' => true]);
        $users = User::all();

        $response = $this->actingAs($user)
                         ->get('/users/');

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
                         ->get('/users/');

        $response->assertStatus(200);

        $response->assertJsonFragment($user2->toArray());
        $response->assertJsonFragment($user3->toArray());
        $response->assertJsonFragment($user4->toArray());
        $response->assertJsonMissing(['id' => $user1->id]);
        $response->assertJsonMissing(['id' => $detachedUser->id]);
    }
}
