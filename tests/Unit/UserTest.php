<?php

namespace App;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(\RootSeeder::class);
    }

    public function testIsAdmin__superAdmin()
    {
        $user = factory(User::class)->make(['super_admin' => true]);
        $this->assertTrue($user->isAdmin());
    }

    public function testIsAdmin__nonSuperAdmin()
    {
        $user = factory(User::class)->make(['super_admin' => false]);
        $this->assertFalse($user->isAdmin());
    }

    public function testIsAdmin__nonAdmin()
    {
        $user = factory(User::class)->create(['super_admin' => false]);
        $user->roles()
             ->save(factory(Role::class)->make(['admin' => false]));

        $this->assertFalse($user->isAdmin());
    }

    public function testIsAdmin__admin()
    {
        $user = factory(User::class)->create(['super_admin' => false]);
        $user->roles()
             ->save(factory(Role::class)->make(['admin' => true]));

        $this->assertTrue($user->isAdmin());
    }

    public function testCanManageGroup__superAdmin()
    {
        $user  = factory(User::class)->create(['super_admin' => true]);
        $group = factory(Group::class)->create();

        $this->assertTrue($user->canManageGroup($group));
    }

    public function testCanManageGroup__admin()
    {
        $user  = factory(User::class)->create(['super_admin' => false]);
        $group = factory(Group::class)->create();
        $user->roles()
             ->save(factory(Role::class)->make([
                 'admin'    => true,
                 'group_id' => $group->id,
             ]));

        $this->assertTrue($user->canManageGroup($group));
    }

    public function testCanManageChildGroup__admin()
    {
        $user  = factory(User::class)->create(['super_admin' => false]);
        $group = factory(Group::class)->create();
        $user->roles()
             ->save(factory(Role::class)->make([
                 'admin'    => true,
                 'group_id' => $group->id,
             ]));
        $childGroup = factory(Group::class)->create(['parent_id' => $group->id]);

        $this->assertTrue($user->canManageGroup($childGroup));
    }

    public function testCanManageGroup__nonAdmin()
    {
        $user  = factory(User::class)->create(['super_admin' => false]);
        $group = factory(Group::class)->create();
        $user->roles()
             ->save(factory(Role::class)->make([
                 'admin'    => false,
                 'group_id' => $group->id,
             ]));

        $this->assertFalse($user->canManageGroup($group));
    }
}
