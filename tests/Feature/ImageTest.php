<?php

namespace Tests\Feature;

use App\Image;
use App\Legal;
use App\Logo;
use App\User;
use Illuminate\Support\Facades\DB;
use RootSeeder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImageTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RootSeeder::class);
    }

    public function testGetImage__own__200()
    {
        $user  = factory(User::class)->create(['enabled' => true]);
        $image = factory(Image::class)->create([
            'user_id' => $user->id,
            'type'    => Image::TYPE_RAW
        ]);
        factory(Legal::class)->create([
            'shared'   => false,
            'image_id' => $image->id
        ]);

        $response = $this->actingAs($user)
                         ->get("/api/1/images/{$image->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id',
                     'user' => [
                         'id',
                         'first_name',
                         'last_name',
                         'email'
                     ],
                     'src',
                     'thumb_src',
                     'file_type',
                     'width',
                     'height',
                     'created_at'
                 ]);
    }

    public function testGetImage__final__200()
    {
        $user  = factory(User::class)->create(['enabled' => true]);
        $image = factory(Image::class)->create([
            'type' => Image::TYPE_FINAL
        ]);

        $response = $this->actingAs($user)
                         ->get("/api/1/images/{$image->id}");

        $response->assertStatus(200);
    }

    public function testGetImage__shared__200()
    {
        $user  = factory(User::class)->create(['enabled' => true]);
        $image = factory(Image::class)->create([
            'type' => Image::TYPE_RAW
        ]);
        factory(Legal::class)->create([
            'shared'   => true,
            'image_id' => $image->id
        ]);

        $response = $this->actingAs($user)
                         ->get("/api/1/images/{$image->id}");

        $response->assertStatus(200);
    }

    public function testGetImage__nonShared__403()
    {
        $user  = factory(User::class)->create(['enabled' => true]);
        $image = factory(Image::class)->create([
            'type' => Image::TYPE_RAW
        ]);
        factory(Legal::class)->create([
            'shared'   => false,
            'image_id' => $image->id
        ]);

        $response = $this->actingAs($user)
                         ->get("/api/1/images/{$image->id}");

        $response->assertStatus(403);
    }

    public function testGetRawImages__200()
    {
        $user = factory(User::class)->create(['enabled' => true]);

        $shared1   = factory(Image::class)->create([
            'type' => Image::TYPE_RAW
        ]);
        $shared2   = factory(Image::class)->create([
            'type' => Image::TYPE_RAW
        ]);
        $nonShared = factory(Image::class)->create([
            'type' => Image::TYPE_RAW
        ]);
        factory(Legal::class)->create([
            'shared'   => true,
            'image_id' => $shared1->id
        ]);
        factory(Legal::class)->create([
            'shared'   => true,
            'image_id' => $shared2->id
        ]);
        factory(Legal::class)->create([
            'shared'   => false,
            'image_id' => $nonShared->id
        ]);

        $response = $this->actingAs($user)
                         ->get("/api/1/images/raw");

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $shared1->id])
                 ->assertJsonFragment(['id' => $shared2->id])
                 ->assertJsonMissing(['id' => $nonShared->id]);
    }

    public function testGetFinalImages__200()
    {
        $user = factory(User::class)->create(['enabled' => true]);

        $final1 = factory(Image::class)->create([
            'type' => Image::TYPE_FINAL
        ]);
        $final2 = factory(Image::class)->create([
            'type' => Image::TYPE_FINAL
        ]);
        $raw    = factory(Image::class)->create([
            'type' => Image::TYPE_RAW
        ]);

        $response = $this->actingAs($user)
                         ->get("/api/1/images/final");

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $final1->id])
                 ->assertJsonFragment(['id' => $final2->id])
                 ->assertJsonMissing(['id' => $raw->id]);
    }

    public function testDeleteImage__own__204()
    {
        $user  = factory(User::class)->create(['enabled' => true]);
        $image = factory(Image::class)->create([
            'user_id' => $user->id,
        ]);
        factory(Legal::class)->create([
            'image_id' => $image->id
        ]);

        $response = $this->actingAs($user)
                         ->delete("/api/1/images/{$image->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('legals', [
            'image_id'   => $image->id,
            'deleted_at' => null
        ]);
        $this->assertDatabaseMissing('images', [
            'id'         => $image->id,
            'deleted_at' => null
        ]);
    }

    public function testDeleteImage__others__403()
    {
        $user  = factory(User::class)->create(['enabled' => true]);
        $image = factory(Image::class)->create();
        factory(Legal::class)->create([
            'image_id' => $image->id
        ]);

        $response = $this->actingAs($user)
                         ->delete("/api/1/images/{$image->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('legals', [
            'image_id'   => $image->id,
            'deleted_at' => null
        ]);
        $this->assertDatabaseHas('images', [
            'id'         => $image->id,
            'deleted_at' => null
        ]);
    }

    public function testDeleteImage__othersSuperAdmin__204()
    {
        $user  = factory(User::class)->create([
            'super_admin' => true,
            'enabled'     => true
        ]);
        $image = factory(Image::class)->create();
        factory(Legal::class)->create([
            'image_id' => $image->id
        ]);

        $response = $this->actingAs($user)
                         ->delete("/api/1/images/{$image->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('legals', [
            'image_id'   => $image->id,
            'deleted_at' => null
        ]);
        $this->assertDatabaseMissing('images', [
            'id'         => $image->id,
            'deleted_at' => null
        ]);
    }

    public function testPostImage__raw__200()
    {
        $user     = factory(User::class)->create(['enabled' => true]);
        $filename = 'Image007.png';
        $payload  = [
            'base64data' => 'data:application/octet-stream;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAMAAADXqc3KAAAAA3NCSVQICAjb4U/gAAAACXBIWXMAAACmAAAApgHdff84AAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAAAMBQTFRF//////9V/79A/8wz/99A/9VA/89A/+lQ/+dN/+NO/+VO/+VR/+ZQ/+ZR/+VQ/+ZQ/+ZP/+ZR/+dQ/9dA/9hC/+ZQ/9dB/+dQ/+dQ/+FL/9dA/9dA/9dC/9tF/9tF/99J/99I/9dB/9hD/9dB/9hC/9hD/9lD/tZB/+NN/tZA/uNO/9dB/9dB/+ZQ/+VP/9dB/NA//9dB/+ZQtNJGvMZDxbhAzqs90qU826w83K4877w+770++M1E/NBA/9dB/+ZQfYTn/wAAADN0Uk5TAAMEBQgMECMrLjFFRmhtcHF7f4eIj5GSk6nGysrOz9bX2drb4ODi8PHy8/X5+fv8/v7+6tieJQAAAS5JREFUKFN9UldzwmAMEyEQKIQSymjYe+/1QQDp//+rPpAS6LXVk0+6s2zZwB0WAM8Li2d0inFnsXDixc4rn9iw7EpumZtExGZyKXvOlS/5K87tt/dMKOS0LdXIptQk659SLhRSW6lF9qU+2ZK2qbC/XZI04Gi5HHEgqWQnAFidzbzWlsbr2/V6W4+ldm2+6VhAkSSHUwVnY86BpkOS/AAQL69Iri/GHI/GXNYkV+U4AMf1G93J7WQO+/3BnG6TbsN3HcBbSNLyao773W5/NNelJC28vwU4rt/sj6JWo37Td53IPLibB5F5kSQHYwUnY06BxoNwXKvHWbX1vGCrOmPPApCM/Ywklvw/ROSkSv0ReyWKPZtPPx8qnc/igd9PCwC972fovfKwARQKYQEAX2+6R0mYkO06AAAAAElFTkSuQmCC',
            'part'       => 0,
            'filename'   => $filename,
        ];

        $response = $this->actingAs($user)
                         ->postJson("/api/1/files/images", $payload);

        $response->assertStatus(200);

        /**
         * Above was precondition, the real test starts here
         */
        $image = factory(Image::class)->make([
            'background' => Image::BG_CUSTOM,
            'type'       => Image::TYPE_RAW,
            'logo_id'    => null,
        ]);

        $image->id        = 1; // needed for route creation of image url
        $data             = $image->toArray();
        $data['filename'] = $filename; // excluded from toArray method

        unset($data['id']); // but must not be present in post data
        unset($data['user_id']);
        unset($data['width']);
        unset($data['height']);

        $response = $this->actingAs($user)
                         ->postJson("/api/1/images", $data);

        $imageId = $response->json('id');

        $response->assertStatus(201);
        $response->assertJsonFragment(['user_id' => $user->id]);
        $response->assertJsonFragment(['width' => 24]);
        $response->assertJsonFragment(['height' => 24]);
        $response->assertJsonFragment(['src' => route('image', ['image' => $imageId])]);
        $response->assertJsonFragment(['thumb_src' => route('thumbnail', ['image' => $imageId])]);
        $response->assertJsonFragment(['file_type' => 'png']);
        $response->assertJsonMissing(['filename']);
        $response->assertJsonMissing(['deleted_at']);
        $this->assertDatabaseHas('images', [
            'id' => $imageId
        ]);

        $finalFilename = DB::table('images')->find($imageId)->filename;
        $this->assertFileExists(disk_path(Image::getImageStorageDir().'/'.$finalFilename));
        $this->assertFileExists(disk_path(Image::getThumbnailStorageDir().'/'.$finalFilename));
    }

    public function testPutImage__raw__200()
    {
        $user = factory(User::class)->create(['enabled' => true]);

        $image = factory(Image::class)->create([
            'background' => Image::BG_CUSTOM,
            'type'       => Image::TYPE_RAW,
            'user_id'    => $user->id,
        ]);

        $filename = 'Image007.png';
        $payload  = [
            'base64data' => 'data:application/octet-stream;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAMAAADXqc3KAAAAA3NCSVQICAjb4U/gAAAACXBIWXMAAACmAAAApgHdff84AAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAAAMBQTFRF//////9V/79A/8wz/99A/9VA/89A/+lQ/+dN/+NO/+VO/+VR/+ZQ/+ZR/+VQ/+ZQ/+ZP/+ZR/+dQ/9dA/9hC/+ZQ/9dB/+dQ/+dQ/+FL/9dA/9dA/9dC/9tF/9tF/99J/99I/9dB/9hD/9dB/9hC/9hD/9lD/tZB/+NN/tZA/uNO/9dB/9dB/+ZQ/+VP/9dB/NA//9dB/+ZQtNJGvMZDxbhAzqs90qU826w83K4877w+770++M1E/NBA/9dB/+ZQfYTn/wAAADN0Uk5TAAMEBQgMECMrLjFFRmhtcHF7f4eIj5GSk6nGysrOz9bX2drb4ODi8PHy8/X5+fv8/v7+6tieJQAAAS5JREFUKFN9UldzwmAMEyEQKIQSymjYe+/1QQDp//+rPpAS6LXVk0+6s2zZwB0WAM8Li2d0inFnsXDixc4rn9iw7EpumZtExGZyKXvOlS/5K87tt/dMKOS0LdXIptQk659SLhRSW6lF9qU+2ZK2qbC/XZI04Gi5HHEgqWQnAFidzbzWlsbr2/V6W4+ldm2+6VhAkSSHUwVnY86BpkOS/AAQL69Iri/GHI/GXNYkV+U4AMf1G93J7WQO+/3BnG6TbsN3HcBbSNLyao773W5/NNelJC28vwU4rt/sj6JWo37Td53IPLibB5F5kSQHYwUnY06BxoNwXKvHWbX1vGCrOmPPApCM/Ywklvw/ROSkSv0ReyWKPZtPPx8qnc/igd9PCwC972fovfKwARQKYQEAX2+6R0mYkO06AAAAAElFTkSuQmCC',
            'part'       => 0,
            'filename'   => $filename,
        ];

        $response = $this->actingAs($user)
                         ->postJson("/api/1/files/images", $payload);

        $response->assertStatus(200);

        /**
         * Above was precondition, the real test starts here
         */
        $data['filename'] = $filename; // excluded from toArray method

        $response = $this->actingAs($user)
                         ->putJson("/api/1/images/{$image->id}", $data);

        $response->assertStatus(200);
        $response->assertJsonFragment(['user_id' => $user->id]);
        $response->assertJsonFragment(['width' => 24]);
        $response->assertJsonFragment(['height' => 24]);

        $finalFilename = DB::table('images')->find($image->id)->filename;
        $this->assertFileExists(disk_path(Image::getImageStorageDir().'/'.$finalFilename));
    }

    public function testPutImage__rawOriginalId__422()
    {
        $user = factory(User::class)->create([
            'super_admin' => true,
            'enabled'     => true,
        ]);

        $original = factory(Image::class)->create([
            'background' => Image::BG_CUSTOM,
            'type'       => Image::TYPE_RAW,
            'user_id'    => $user->id,
        ]);
        factory(Legal::class)->create([
            'shared'   => true,
            'image_id' => $original->id
        ]);

        $image = factory(Image::class)->create([
            'background' => Image::BG_CUSTOM,
            'type'       => Image::TYPE_RAW,
            'user_id'    => $user->id,
        ]);

        $data['original_id'] = $original->id;

        $response = $this->actingAs($user)
                         ->putJson("/api/1/images/{$image->id}", $data);

        $response->assertStatus(422);
    }

    public function testPutImage__rawLogoId__422()
    {
        $user = factory(User::class)->create([
            'super_admin' => true,
            'enabled'     => true,
        ]);

        $image = factory(Image::class)->create([
            'background' => Image::BG_CUSTOM,
            'type'       => Image::TYPE_RAW,
            'user_id'    => $user->id,
        ]);

        $data['logo_id'] = factory(Logo::class)->create()->id;

        $response = $this->actingAs($user)
                         ->putJson("/api/1/images/{$image->id}", $data);

        $response->assertStatus(422);
    }

    public function testPutImage__rawBackground__422()
    {
        $user = factory(User::class)->create([
            'super_admin' => true,
            'enabled'     => true,
        ]);

        $image = factory(Image::class)->create([
            'background' => Image::BG_CUSTOM,
            'type'       => Image::TYPE_RAW,
            'user_id'    => $user->id,
        ]);

        $data['background'] = Image::BG_GRADIENT;

        $response = $this->actingAs($user)
                         ->putJson("/api/1/images/{$image->id}", $data);

        $response->assertStatus(422);
    }

    public function testPutImage__finalGradientOriginalId__422()
    {
        $user = factory(User::class)->create([
            'super_admin' => true,
            'enabled'     => true,
        ]);

        $original = factory(Image::class)->create([
            'background' => Image::BG_CUSTOM,
            'type'       => Image::TYPE_RAW,
            'user_id'    => $user->id,
        ]);
        factory(Legal::class)->create([
            'shared'   => true,
            'image_id' => $original->id
        ]);

        $image = factory(Image::class)->create([
            'background' => Image::BG_GRADIENT,
            'type'       => Image::TYPE_FINAL,
            'user_id'    => $user->id,
        ]);

        $data['original_id'] = $original->id;

        $response = $this->actingAs($user)
                         ->putJson("/api/1/images/{$image->id}", $data);

        $response->assertStatus(422);
    }

    public function testPutImage__finalSharedOriginalId__200()
    {
        $user = factory(User::class)->create([
            'super_admin' => true,
            'enabled'     => true,
        ]);

        $original = factory(Image::class)->create([
            'background' => Image::BG_CUSTOM,
            'type'       => Image::TYPE_RAW,
            'user_id'    => $user->id,
        ]);
        factory(Legal::class)->create([
            'shared'   => true,
            'image_id' => $original->id
        ]);

        $image = factory(Image::class)->create([
            'background' => Image::BG_CUSTOM,
            'type'       => Image::TYPE_FINAL,
            'user_id'    => $user->id,
        ]);

        $data['original_id'] = $original->id;

        $response = $this->actingAs($user)
                         ->putJson("/api/1/images/{$image->id}", $data);

        $response->assertStatus(200);
    }

    public function testPutImage__finalNonSharedOriginalId__422()
    {
        $user      = factory(User::class)->create(['enabled' => true]);
        $otherUser = factory(User::class)->create();

        $original = factory(Image::class)->create([
            'background' => Image::BG_CUSTOM,
            'type'       => Image::TYPE_RAW,
            'user_id'    => $otherUser,
        ]);
        factory(Legal::class)->create([
            'shared'   => false,
            'image_id' => $original->id
        ]);

        $image = factory(Image::class)->create([
            'background' => Image::BG_CUSTOM,
            'type'       => Image::TYPE_FINAL,
            'user_id'    => $user->id,
        ]);

        $data['original_id'] = $original->id;

        $response = $this->actingAs($user)
                         ->putJson("/api/1/images/{$image->id}", $data);

        $response->assertStatus(422);
    }

    public function testPutImage__finalOwnOriginalId__422()
    {
        $user = factory(User::class)->create([
            'super_admin' => true,
            'enabled'     => true,
        ]);

        $image = factory(Image::class)->create([
            'background' => Image::BG_CUSTOM,
            'type'       => Image::TYPE_FINAL,
            'user_id'    => $user->id,
        ]);
        factory(Legal::class)->create([
            'shared'   => true,
            'image_id' => $image->id
        ]);

        $data['original_id'] = $image->id;

        $response = $this->actingAs($user)
                         ->putJson("/api/1/images/{$image->id}", $data);

        $response->assertStatus(422);
    }

    public function testPutImage__finalNoOriginalId__422()
    {
        $user = factory(User::class)->create([
            'super_admin' => true,
            'enabled'     => true,
        ]);

        $image = factory(Image::class)->create([
            'background' => Image::BG_CUSTOM,
            'type'       => Image::TYPE_FINAL,
            'user_id'    => $user->id,
        ]);

        $data['original_id'] = null;

        $response = $this->actingAs($user)
                         ->putJson("/api/1/images/{$image->id}", $data);

        $response->assertStatus(422);
    }

    public function testPutImage__finalNoOriginalId__200()
    {
        $user = factory(User::class)->create([
            'super_admin' => true,
            'enabled'     => true,
        ]);

        $image = factory(Image::class)->create([
            'background' => Image::BG_TRANSPARENT,
            'type'       => Image::TYPE_FINAL,
            'user_id'    => $user->id,
        ]);

        $data['original_id'] = null;

        $response = $this->actingAs($user)
                         ->putJson("/api/1/images/{$image->id}", $data);

        $response->assertStatus(200);
    }
}
