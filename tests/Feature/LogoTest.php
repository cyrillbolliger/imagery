<?php

namespace Tests\Feature;

use App\Group;
use App\Logo;
use App\Role;
use App\User;
use Illuminate\Support\Facades\DB;
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
                         ->getJson("/api/1/logos/$logo->id");

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $logo->id]);
        $response->assertJsonFragment(['src' => route('logo', ['logo' => $logo->id])]);
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

        $filename = 'Image007.png';
        $payload  = [
            'base64data' => 'data:application/octet-stream;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAMAAADXqc3KAAAAA3NCSVQICAjb4U/gAAAACXBIWXMAAACmAAAApgHdff84AAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAAAMBQTFRF//////9V/79A/8wz/99A/9VA/89A/+lQ/+dN/+NO/+VO/+VR/+ZQ/+ZR/+VQ/+ZQ/+ZP/+ZR/+dQ/9dA/9hC/+ZQ/9dB/+dQ/+dQ/+FL/9dA/9dA/9dC/9tF/9tF/99J/99I/9dB/9hD/9dB/9hC/9hD/9lD/tZB/+NN/tZA/uNO/9dB/9dB/+ZQ/+VP/9dB/NA//9dB/+ZQtNJGvMZDxbhAzqs90qU826w83K4877w+770++M1E/NBA/9dB/+ZQfYTn/wAAADN0Uk5TAAMEBQgMECMrLjFFRmhtcHF7f4eIj5GSk6nGysrOz9bX2drb4ODi8PHy8/X5+fv8/v7+6tieJQAAAS5JREFUKFN9UldzwmAMEyEQKIQSymjYe+/1QQDp//+rPpAS6LXVk0+6s2zZwB0WAM8Li2d0inFnsXDixc4rn9iw7EpumZtExGZyKXvOlS/5K87tt/dMKOS0LdXIptQk659SLhRSW6lF9qU+2ZK2qbC/XZI04Gi5HHEgqWQnAFidzbzWlsbr2/V6W4+ldm2+6VhAkSSHUwVnY86BpkOS/AAQL69Iri/GHI/GXNYkV+U4AMf1G93J7WQO+/3BnG6TbsN3HcBbSNLyao773W5/NNelJC28vwU4rt/sj6JWo37Td53IPLibB5F5kSQHYwUnY06BxoNwXKvHWbX1vGCrOmPPApCM/Ywklvw/ROSkSv0ReyWKPZtPPx8qnc/igd9PCwC972fovfKwARQKYQEAX2+6R0mYkO06AAAAAElFTkSuQmCC',
            'part'       => 0,
            'filename'   => $filename,
        ];

        $response = $this->actingAs($manager)
                         ->postJson("/api/1/files/logos", $payload);

        $response->assertStatus(200);

        /**
         * Above was precondition, the real test starts here
         */
        $logo->name       = 'gruene-zh.ch';
        $data             = $logo->toArray();
        $data['filename'] = $filename; // excluded from toArray method

        $response = $this->actingAs($manager)
                         ->putJson("/api/1/logos/$logo->id", $data);

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => $logo->name]);
        $response->assertJsonMissing(['filename']);
        $this->assertDatabaseHas('logos', [
            'id'   => $logo->id,
            'name' => $logo->name
        ]);

        $finalFilename = DB::table('logos')->find($logo->id)->filename;
        $this->assertFileExists(disk_path(config('app.logo_dir').'/'.$finalFilename));
    }

    public function testPutLogo__adminInvalidMimeType__422()
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

        $filename = 'Image007.png';
        $payload  = [
            'base64data' => 'data:application/octet-stream;base64,PD9waHAKCmRpZSgnaGFyZCcpOwo=', // a simple php file
            'part'       => 0,
            'filename'   => $filename,
        ];

        $response = $this->actingAs($manager)
                         ->postJson("/api/1/files/logos", $payload);

        $response->assertStatus(200);

        /**
         * Above was precondition, the real test starts here
         */
        $logo->name       = 'gruene-zh.ch';
        $data             = $logo->toArray();
        $data['filename'] = $filename; // excluded from toArray method

        $response = $this->actingAs($manager)
                         ->putJson("/api/1/logos/$logo->id", $data);

        $response->assertStatus(422);
        $response->assertJsonPath('errors.file.0', 'The uploaded file has an invalid mime type.');
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

        // we're soft deleting, so the file must stay
        $this->assertFileExists(disk_path(config('app.logo_dir').'/'.$logo->filename));
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

        $filename = 'NewLogo.png';
        $payload  = [
            'base64data' => 'data:application/octet-stream;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAMAAADXqc3KAAAAA3NCSVQICAjb4U/gAAAACXBIWXMAAACmAAAApgHdff84AAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAAAMBQTFRF//////9V/79A/8wz/99A/9VA/89A/+lQ/+dN/+NO/+VO/+VR/+ZQ/+ZR/+VQ/+ZQ/+ZP/+ZR/+dQ/9dA/9hC/+ZQ/9dB/+dQ/+dQ/+FL/9dA/9dA/9dC/9tF/9tF/99J/99I/9dB/9hD/9dB/9hC/9hD/9lD/tZB/+NN/tZA/uNO/9dB/9dB/+ZQ/+VP/9dB/NA//9dB/+ZQtNJGvMZDxbhAzqs90qU826w83K4877w+770++M1E/NBA/9dB/+ZQfYTn/wAAADN0Uk5TAAMEBQgMECMrLjFFRmhtcHF7f4eIj5GSk6nGysrOz9bX2drb4ODi8PHy8/X5+fv8/v7+6tieJQAAAS5JREFUKFN9UldzwmAMEyEQKIQSymjYe+/1QQDp//+rPpAS6LXVk0+6s2zZwB0WAM8Li2d0inFnsXDixc4rn9iw7EpumZtExGZyKXvOlS/5K87tt/dMKOS0LdXIptQk659SLhRSW6lF9qU+2ZK2qbC/XZI04Gi5HHEgqWQnAFidzbzWlsbr2/V6W4+ldm2+6VhAkSSHUwVnY86BpkOS/AAQL69Iri/GHI/GXNYkV+U4AMf1G93J7WQO+/3BnG6TbsN3HcBbSNLyao773W5/NNelJC28vwU4rt/sj6JWo37Td53IPLibB5F5kSQHYwUnY06BxoNwXKvHWbX1vGCrOmPPApCM/Ywklvw/ROSkSv0ReyWKPZtPPx8qnc/igd9PCwC972fovfKwARQKYQEAX2+6R0mYkO06AAAAAElFTkSuQmCC',
            'part'       => 0,
            'filename'   => $filename,
        ];

        $response = $this->actingAs($manager)
                         ->postJson("/api/1/files/logos", $payload);

        $response->assertStatus(200);

        /**
         * Above was precondition, the real test starts here
         */
        $logo             = factory(Logo::class)->make([
            'name' => Str::random(),
        ]);
        $data             = $logo->toArray();
        $data['filename'] = $filename; // excluded from toArray method
        $data['groups']   = [$group->id];
        unset($data['added_by']); // not mutable

        $response = $this->actingAs($manager)
                         ->postJson("/api/1/logos", $data);

        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => $logo->name]);
        $response->assertJsonMissing(['filename']);
        $this->assertDatabaseHas('logos', [
            'name' => $logo->name
        ]);

        $finalFilename = DB::table('logos')->find($response->json('id'))->filename;
        $this->assertFileExists(disk_path(config('app.logo_dir').'/'.$finalFilename));
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
