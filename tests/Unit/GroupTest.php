<?php

namespace App;


use Database\Seeders\RootSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RootSeeder::class);
    }

    public function testUsersBelow()
    {
        $root       = factory(Group::class)->create();
        $child      = factory(Group::class)->create(['parent_id' => $root->id]);
        $grandchild = factory(Group::class)->create(['parent_id' => $child->id]);

        $user1 = $root->users()->save(factory(User::class)->make());
        $user2 = $child->users()->save(factory(User::class)->make());
        $user3 = $grandchild->users()->save(factory(User::class)->make());

        $detachedUser = factory(User::class)->create();

        $usersBelow = $root->usersBelow();
        $ids        = $usersBelow->pluck('id');

        $this->assertTrue($ids->contains($user1->id));
        $this->assertTrue($ids->contains($user2->id));
        $this->assertTrue($ids->contains($user3->id));
        $this->assertFalse($ids->contains($detachedUser->id));
    }
}
