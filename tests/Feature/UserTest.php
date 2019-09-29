<?php

namespace Tests\Feature;

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

    }
}
