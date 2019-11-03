<?php

namespace Tests\Feature;

use App\Image;
use App\Legal;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RootSeeder;
use Tests\TestCase;

class LegalTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RootSeeder::class);
    }

    public function testGet_200()
    {
        $user  = factory(User::class)->create();
        $image = factory(Image::class)->create([
            'user_id' => $user->id,
            'type'    => Image::TYPE_RAW
        ]);
        $legal = factory(Legal::class)->create([
            'shared'   => false,
            'image_id' => $image->id
        ]);

        $response = $this->actingAs($user)
                         ->getJson("/legals/{$legal->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id',
                     'image_id',
                     'right_of_personality',
                     'originator_type',
                     'licence',
                     'originator',
                     'stock_url',
                     'shared',
                     'created_at',
                     'updated_at',
                     'deleted_at',
                 ]);
    }

    public function testGetIndex_200()
    {
        $user   = factory(User::class)->create();
        $image1 = factory(Image::class)->create([
            'type' => Image::TYPE_RAW
        ]);
        $legal1 = factory(Legal::class)->create([
            'shared'   => true,
            'image_id' => $image1->id
        ]);
        $image2 = factory(Image::class)->create([
            'type' => Image::TYPE_RAW
        ]);
        $legal2 = factory(Legal::class)->create([
            'shared'   => true,
            'image_id' => $image2->id
        ]);
        $image3 = factory(Image::class)->create([
            'type' => Image::TYPE_RAW
        ]);
        $legal3 = factory(Legal::class)->create([
            'shared'   => false,
            'image_id' => $image3->id
        ]);

        $response = $this->actingAs($user)
                         ->getJson("/legals");

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $legal1->id])
                 ->assertJsonFragment(['id' => $legal2->id])
                 ->assertJsonMissing(['id' => $legal3->id]);
    }

    public function testPut_200()
    {
        $user  = factory(User::class)->create();
        $image = factory(Image::class)->create([
            'user_id' => $user->id,
            'type'    => Image::TYPE_RAW
        ]);
        $legal = factory(Legal::class)->create([
            'image_id' => $image->id
        ]);

        $legal->right_of_personality = Legal::PERSONALITY_PUBLIC_INTEREST;
        $legal->originator_type      = Legal::ORIGINATOR_AGENCY;
        $legal->originator           = 'The star photographer';
        $legal->licence              = Legal::LICENCE_CC_ATTRIBUTION;
        $legal->stock_url            = 'https://starphtotographer.example/best-pic';
        $legal->shared               = false;

        $response = $this->actingAs($user)
                         ->putJson("/legals/{$legal->id}", $legal->toArray());

        $response->assertStatus(200);
    }
}
