<?php

namespace Tests\Feature;

use App\Image;
use App\Legal;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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
                         ->getJson("/api/1/images/{$image->id}/legal");

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
                 ])
                 ->assertJsonFragment(['id' => $legal->id]);
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
                         ->putJson("/api/1/images/{$image->id}/legal", $legal->toArray());

        $response->assertStatus(200);
        $this->assertDatabaseHas('legals', [
            'id'                   => $legal->id,
            'right_of_personality' => $legal->right_of_personality,
            'originator_type'      => $legal->originator_type,
            'originator'           => $legal->originator,
            'licence'              => $legal->licence,
            'stock_url'            => $legal->stock_url,
            'shared'               => $legal->shared,
        ]);
    }

    public function testPost_201()
    {
        $user  = factory(User::class)->create();
        $image = factory(Image::class)->create([
            'user_id' => $user->id,
            'type'    => Image::TYPE_RAW
        ]);
        $legal = factory(Legal::class)->make([
            'licence'   => Legal::LICENCE_CC,
            'stock_url' => 'https://google.com'
        ]);

        $data = $legal->toArray();
        unset($data['image_id']);

        $response = $this->actingAs($user)
                         ->postJson("/api/1/images/{$image->id}/legal", $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('legals', [
            'image_id' => $image->id
        ]);
    }

    public function testPost_saveMultiple_409()
    {
        $user  = factory(User::class)->create();
        $image = factory(Image::class)->create([
            'user_id' => $user->id,
            'type'    => Image::TYPE_RAW
        ]);
        $legal = factory(Legal::class)->create([
            'licence'   => Legal::LICENCE_CC,
            'stock_url' => 'https://google.com',
            'image_id'  => $image->id,
        ]);

        $data = $legal->toArray();
        unset($data['id']);
        unset($data['image_id']);
        unset($data['created_at']);
        unset($data['updated_at']);

        $response = $this->actingAs($user)
                         ->postJson("/api/1/images/{$image->id}/legal", $data);

        $response->assertStatus(409);
        $this->assertEquals(1, DB::table('legals')->where('image_id', $image->id)->count());
    }
}
