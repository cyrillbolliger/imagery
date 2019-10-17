<?php

namespace Tests\Feature;

use App\Group;
use App\Logo;
use App\Role;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RootSeeder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RootSeeder::class);
    }

    public function testGetLogo__user__200()
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
                         ->getJson("/logos/$logo->id");

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $logo->id]);
        $response->assertJsonFragment(['src' => route('logo', ['logo' => $logo->id])]);
    }

    public function testGetLogo__nonAttachedUser__403()
    {
        $manager = factory(User::class)->create(['super_admin' => false]);
        $logo    = factory(Logo::class)->create();

        $response = $this->actingAs($manager)
                         ->getJson("/logos/$logo->id");

        $response->assertStatus(403);
    }

    public function testGetLogos__user__403()
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
                         ->getJson("/logos");

        $response->assertStatus(403);
    }

    public function testGetLogos__admin__200()
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
                         ->getJson("/logos");

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $logo1->id]);
        $response->assertJsonFragment(['id' => $logo2->id]);
        $response->assertJsonMissing(['id' => $logo3->id]);
    }

    public function testGetLogoFile__user__200()
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
                         ->get("/logos/$logo->id/file");

        $response->assertStatus(200);
    }

    public function testGetLogoFile__nonAttachedUser__403()
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
                         ->get("/logos/$logo->id/file");

        $response->assertStatus(403);
    }

    public function testPutLogo__admin__200()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => true,
                'group_id' => $group->id
            ])
        );

        $logo = factory(Logo::class)->create();
        $group->logos()->attach($logo);

        $logo->name     = 'changed';
        $logo->filename = 'changed_file.svg';

        $response = $this->actingAs($manager)
                         ->putJson("/logos/$logo->id", $logo->toArray());

//        $response->assertStatus(200);
//        $response->assertJsonFragment(['name' => $logo->name]);
//        $this->assertDatabaseHas('logos', [
//            'id'   => $logo->id,
//            'name' => $logo->name
//        ]);
    }
}