<?php

namespace Tests\Feature;

use App\Image;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetRawImages()
    {
        $user = factory(User::class)->create();
        factory(Image::class, 3)->create();

        $response = $this->actingAs($user)
                         ->get('/images/raw');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         0 => [
                             'id',
                             'user' => [
                                 'id', 'name', 'email'
                             ],
                             'filename',
                             'width',
                             'height',
                             'created_at'
                         ]
                     ],
                     'links',
                     'meta'
                 ]);
    }

    public function testGetRawImage()
    {
        $user  = factory(User::class)->create();
        $image = factory(Image::class)->create();

        $response = $this->actingAs($user)
                         ->get("/images/{$image->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id',
                     'user' => [
                         'id', 'name', 'email'
                     ],
                     'filename',
                     'width',
                     'height',
                     'created_at'
                 ]);
    }
}
