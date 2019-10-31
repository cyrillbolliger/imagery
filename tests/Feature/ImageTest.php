<?php

namespace Tests\Feature;

use App\Image;
use App\Legal;
use App\User;
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
        $user  = factory(User::class)->create();
        $image = factory(Image::class)->create([
            'user_id' => $user->id,
            'type'    => Image::TYPE_RAW
        ]);
        factory(Legal::class)->create([
            'shared'   => false,
            'image_id' => $image->id
        ]);

        $response = $this->actingAs($user)
                         ->get("/images/{$image->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id',
                     'user' => [
                         'id',
                         'first_name',
                         'last_name',
                         'email'
                     ],
                     'filename',
                     'width',
                     'height',
                     'created_at'
                 ]);
    }

    public function testGetImage__final__200()
    {
        $user  = factory(User::class)->create();
        $image = factory(Image::class)->create([
            'type' => Image::TYPE_FINAL
        ]);

        $response = $this->actingAs($user)
                         ->get("/images/{$image->id}");

        $response->assertStatus(200);
    }

    public function testGetImage__shared__200()
    {
        $user  = factory(User::class)->create();
        $image = factory(Image::class)->create([
            'type' => Image::TYPE_RAW
        ]);
        factory(Legal::class)->create([
            'shared'   => true,
            'image_id' => $image->id
        ]);

        $response = $this->actingAs($user)
                         ->get("/images/{$image->id}");

        $response->assertStatus(200);
    }

    public function testGetImage__nonShared__403()
    {
        $user  = factory(User::class)->create();
        $image = factory(Image::class)->create([
            'type' => Image::TYPE_RAW
        ]);
        factory(Legal::class)->create([
            'shared'   => false,
            'image_id' => $image->id
        ]);

        $response = $this->actingAs($user)
                         ->get("/images/{$image->id}");

        $response->assertStatus(403);
    }

    public function testGetRawImages__200()
    {
        $user = factory(User::class)->create();

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
                         ->get("/images/raw");

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $shared1->id])
                 ->assertJsonFragment(['id' => $shared2->id])
                 ->assertJsonMissing(['id' => $nonShared->id]);
    }

    public function testGetFinalImages__200()
    {
        $user = factory(User::class)->create();

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
                         ->get("/images/final");

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $final1->id])
                 ->assertJsonFragment(['id' => $final2->id])
                 ->assertJsonMissing(['id' => $raw->id]);
    }
}
