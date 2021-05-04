<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use Database\Seeders\RootSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrowdinTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RootSeeder::class);
    }

    public function testShowCredentials__200()
    {
        $user = factory(User::class)->create(['enabled' => true]);

        $response = $this->actingAs($user)
                         ->get('/api/1/crowdin/credentials');

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'username' => config('app.crowdin_user'),
                     'password' => config('app.crowdin_pass')
                 ]);
    }

    public function testShowCredentials__302()
    {
        $response = $this->get('/api/1/crowdin/credentials');

        $response->assertStatus(302);
    }
}
