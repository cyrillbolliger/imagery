<?php

namespace App;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use RootSeeder;


class UserObserverTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RootSeeder::class);
    }

    public function testCreating()
    {
        $manager = User::first();
        $managed = factory(User::class)->make();

        Auth::login($manager);

        $managed->save();

        $this->assertEquals($manager, $managed->addedBy);
    }

    public function testDeleting()
    {
        $user1 = factory(User::class)->create();
        $email = $user1->email;

        $user1->delete();
        $this->assertEquals("del0000000001 $email", $user1->email);

        $user2 = factory(User::class)->create(['email' => $email]);
        $user2->delete();
        $this->assertEquals("del0000000002 $email", $user2->email);
    }

    public function testRestoring()
    {
        $user  = factory(User::class)->create(['enabled' => true]);
        $email = $user->email;

        $user->delete();
        $this->assertEquals("del0000000001 $email", $user->email);

        $user->restore();
        $this->assertEquals($email, $user->email);
    }
}
