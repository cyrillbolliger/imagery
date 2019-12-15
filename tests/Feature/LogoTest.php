<?php

namespace Tests\Feature;

use App\Group;
use App\Logo;
use App\Role;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

        $this->copyLogosToTestStorage();
    }

    private function copyLogosToTestStorage()
    {
        $dir   = Logo::getStorageDir();
        $files = Storage::disk('local')->files($dir);

        foreach ($files as $file) {
            Storage::put(
                $file,
                Storage::disk('local')->get($file)
            );
        }
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
                         ->getJson("/api/1/logos/$logo->id");

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $logo->id]);
        $response->assertJsonFragment(['src_white' => route('logo', ['logo' => $logo->id, 'color' => 'white'])]);
        $response->assertJsonFragment(['src_green' => route('logo', ['logo' => $logo->id, 'color' => 'green'])]);
    }

    public function testGetLogo__nonAttachedUser__403()
    {
        $manager = factory(User::class)->create(['super_admin' => false]);
        $logo    = factory(Logo::class)->create();

        $response = $this->actingAs($manager)
                         ->getJson("/api/1/logos/$logo->id");

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
                         ->getJson("/api/1/logos");

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
                         ->getJson("/api/1/logos");

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $logo1->id]);
        $response->assertJsonFragment(['id' => $logo2->id]);
        $response->assertJsonMissing(['id' => $logo3->id]);
    }

    public function testGetLogosByGroup__200()
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

        $logo->name = 'gruene-zh.ch';
        $data       = $logo->toArray();

        $response = $this->actingAs($manager)
                         ->putJson("/api/1/logos/$logo->id", $data);

        dd($response);

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => $logo->name]);
        $this->assertDatabaseHas('logos', [
            'id'   => $logo->id,
            'name' => $logo->name
        ]);
    }

    public function testDeleteLogo__admin__200()
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

        $response = $this->actingAs($manager)
                         ->deleteJson("/api/1/logos/$logo->id");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('group_logo', ['logo_id' => $logo->id]);
    }

    public function testDeleteLogo__adminNonAdminGroup__422()
    {
        $group         = factory(Group::class)->create();
        $nonAdminGroup = factory(Group::class)->create();

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => true,
                'group_id' => $group->id
            ])
        );

        $logo = factory(Logo::class)->create();
        $group->logos()->attach($logo);
        $nonAdminGroup->logos()->attach($logo);

        $response = $this->actingAs($manager)
                         ->deleteJson("/api/1/logos/$logo->id");

        $response->assertStatus(422);
        $response->assertJsonPath('errors.groups.0',
            'There is at least one group you can\'t manage that depends on this logo.');
    }

    public function testPostLogo__admin__201()
    {
        $group = factory(Group::class)->create();

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => true,
                'group_id' => $group->id
            ])
        );

        $logo             = factory(Logo::class)->make([
            'name' => Str::random(),
        ]);
        $data             = $logo->toArray();
        $data['groups']   = [$group->id];
        unset($data['added_by']); // not mutable

        $response = $this->actingAs($manager)
                         ->postJson("/api/1/logos", $data);

        dd($response);

        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => $logo->name]);
        $this->assertDatabaseHas('logos', [
            'name' => $logo->name
        ]);
    }

    public function testPutLogo__adminUpdateGroups__200()
    {
        $group  = factory(Group::class)->create();
        $child1 = factory(Group::class)->create(['parent_id' => $group->id]);
        $child2 = factory(Group::class)->create(['parent_id' => $group->id]);

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => true,
                'group_id' => $group->id
            ])
        );

        $logo = factory(Logo::class)->create();
        $group->logos()->attach($logo);

        $data = [
            'groups' => [$child1->id, $child2->id]
        ];

        $response = $this->actingAs($manager)
                         ->putJson("/api/1/logos/$logo->id", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('group_logo', [
            'group_id' => $child1->id,
            'logo_id'  => $logo->id
        ]);
        $this->assertDatabaseHas('group_logo', [
            'group_id' => $child2->id,
            'logo_id'  => $logo->id
        ]);
        $this->assertDatabaseMissing('group_logo', [
            'group_id' => $group->id,
            'logo_id'  => $logo->id
        ]);
    }

    public function testPutLogo__adminUpdateGroupsNonAdmin__422()
    {
        $group      = factory(Group::class)->create();
        $otherGroup = factory(Group::class)->create();

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => true,
                'group_id' => $group->id
            ])
        );

        $logo = factory(Logo::class)->create();
        $group->logos()->attach($logo);

        $data = [
            'groups' => [$group->id, $otherGroup->id]
        ];

        $response = $this->actingAs($manager)
                         ->putJson("/api/1/logos/$logo->id", $data);

        $response->assertStatus(422);
        $response->assertJsonPath('errors.groups.0', 'You can only set groups to groups you\'re admin of.');
    }

    public function testPutLogo__adminUpdateGroupsNoGroups__422()
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

        $data = [
            'groups' => []
        ];

        $response = $this->actingAs($manager)
                         ->putJson("/api/1/logos/$logo->id", $data);

        $response->assertStatus(422);
        $response->assertJsonPath('errors.groups.0', 'The logo must be associated with at least one group.');
    }

    public function testPutLogo__adminUpdateGroupsNonAdminGroups__200()
    {
        $group         = factory(Group::class)->create();
        $nonAdminGroup = factory(Group::class)->create();

        $manager = factory(User::class)->create(['super_admin' => false]);
        $manager->roles()->save(
            factory(Role::class)->make([
                'admin'    => true,
                'group_id' => $group->id
            ])
        );

        $logo = factory(Logo::class)->create();
        $group->logos()->attach($logo);
        $nonAdminGroup->logos()->attach($logo);

        $data = [
            'groups' => []
        ];

        $response = $this->actingAs($manager)
                         ->putJson("/api/1/logos/$logo->id", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('group_logo', [
            'group_id' => $nonAdminGroup->id,
            'logo_id'  => $logo->id
        ]);
        $this->assertDatabaseMissing('group_logo', [
            'group_id' => $group->id,
            'logo_id'  => $logo->id
        ]);
    }
}
