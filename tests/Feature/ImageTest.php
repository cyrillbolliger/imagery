<?php

namespace Tests\Feature;

use App\Image;
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

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetRawImages()
    {
        $user = User::find(1);
        factory(Image::class, 2)->create();

        $response = $this->actingAs($user)
                         ->get('/images/raw');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         0 => [
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
                         ],
                         1
                     ],
                     'links',
                     'meta'
                 ]);
    }

    public function testGetRawImage()
    {
        $user  = User::find(1);
        $image = factory(Image::class)->create();

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

    public function testGetFinalImage__503()
    {
        // not logged in
    }

    public function testGetRawImage__503()
    {
        // not logged in
    }

    public function testGetRawImage__NotShareable__503()
    {
        // logged in but not my image and not shareable
    }

    public function testGetRawImage__shareable()
    {
        // a shareable raw image of someone else
    }

    public function testGetRawImage__superAdmin()
    {
        // all raw images
    }
}
