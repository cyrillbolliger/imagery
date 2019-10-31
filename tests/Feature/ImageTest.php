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
}
