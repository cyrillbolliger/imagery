<?php

namespace Tests\Unit\Observers;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Database\Seeders\RootSeeder;


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

    public function testDeleting_email()
    {
        $user1 = factory(User::class)->create();
        $email = $user1->email;

        $user1->delete();
        $this->assertEquals("del0000000001 $email", $user1->email);

        $user2 = factory(User::class)->create(['email' => $email]);
        $user2->delete();
        $this->assertEquals("del0000000002 $email", $user2->email);
    }

    public function testDeleting_sub_empty()
    {
        $user1 = factory(User::class)->create(['sub' => null]);

        $user1->delete();
        self::assertNull($user1->sub);
    }

    public function testDeleting_sub()
    {
        $user1 = factory(User::class)->create();
        $sub = $user1->sub;

        $user1->delete();
        self::assertEquals("del0000000001 $sub", $user1->sub);

        $user2 = factory(User::class)->create(['sub' => $sub]);
        $user2->delete();
        self::assertEquals("del0000000002 $sub", $user2->sub);
    }

    public function testRestoring_email()
    {
        $user  = factory(User::class)->create(['enabled' => true]);
        $email = $user->email;

        $user->delete();
        $this->assertEquals("del0000000001 $email", $user->email);

        $user->restore();
        $this->assertEquals($email, $user->email);
    }

    public function testRestoring_sub_empty()
    {
        $user  = factory(User::class)->create(['enabled' => true, 'sub' => null]);

        $user->delete();
        $user->restore();
        self::assertNull($user->sub);
    }

    public function testRestoring_sub()
    {
        $user  = factory(User::class)->create(['enabled' => true]);
        $sub = $user->sub;

        $user->delete();
        self::assertEquals("del0000000001 $sub", $user->sub);

        $user->restore();
        self::assertEquals($sub, $user->sub);
    }
}
