<?php

namespace Tests\Feature;

use App\Domain\UploadHandler;
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
                         ->postJson("/files/logos", $payload);

        $response->assertStatus(200);

        /**
         * Above was precondition, the real test starts here
         */
        $logo->name       = 'gruene-zh.ch';
        $data             = $logo->toArray();
        $data['filename'] = $filename; // excluded from toArray method

        $tempFilename   = UploadHandler::computeTmpFilename($filename);
        $relTmpFilePath = UploadHandler::getRelDirPath().DIRECTORY_SEPARATOR.$tempFilename;
        $finalFilename  = UploadHandler::computeFinalFilename($relTmpFilePath).'.png';

        $response = $this->actingAs($manager)
                         ->putJson("/logos/$logo->id", $data);

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => $logo->name]);
        $response->assertJsonMissing(['filename']);
        $this->assertDatabaseHas('logos', [
            'id'   => $logo->id,
            'name' => $logo->name
        ]);

        $this->assertFileExists(disk_path(config('app.logo_dir').'/'.$finalFilename));
    }
}
