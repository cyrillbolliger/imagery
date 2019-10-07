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

    public function testCanUseGroup__nonAdmin()
    {
        $user  = factory(User::class)->create(['super_admin' => false]);
        $group = factory(Group::class)->create();
        $user->roles()
             ->save(factory(Role::class)->make([
                 'admin'    => false,
                 'group_id' => $group->id,
             ]));

        $this->assertTrue($user->canUseGroup($group));
    }

    public function testCanUseChildGroup__nonAdmin()
    {
        $user  = factory(User::class)->create(['super_admin' => false]);
        $group = factory(Group::class)->create();
        $user->roles()
             ->save(factory(Role::class)->make([
                 'admin'    => false,
                 'group_id' => $group->id,
             ]));
        $childGroup = factory(Group::class)->create(['parent_id' => $group->id]);

        $this->assertFalse($user->canUseGroup($childGroup));
    }

    public function testCanUseChildGroup__admin()
    {
        $user  = factory(User::class)->create(['super_admin' => false]);
        $group = factory(Group::class)->create();
        $user->roles()
             ->save(factory(Role::class)->make([
                 'admin'    => true,
                 'group_id' => $group->id,
             ]));
        $childGroup = factory(Group::class)->create(['parent_id' => $group->id]);

        $this->assertTrue($user->canUseGroup($childGroup));
    }

    public function testCanUseLogo__admin()
    {
        $user  = factory(User::class)->create(['super_admin' => false]);
        $group = factory(Group::class)->create();
        $user->roles()
             ->save(factory(Role::class)->make([
                 'admin'    => true,
                 'group_id' => $group->id,
             ]));
        $childGroup = factory(Group::class)->create(['parent_id' => $group->id]);
        $logo       = factory(Logo::class)->create();
        $childGroup->logos()->attach($logo->id);
        $childGroup->save();

        $this->assertTrue($user->canUseLogo($logo));
    }

    public function testCanUseLogo__nonAdmin()
    {
        $user  = factory(User::class)->create(['super_admin' => false]);
        $group = factory(Group::class)->create();
        $user->roles()
             ->save(factory(Role::class)->make([
                 'admin'    => false,
                 'group_id' => $group->id,
             ]));
        $childGroup = factory(Group::class)->create(['parent_id' => $group->id]);

        $logo1 = factory(Logo::class)->create();
        $group->logos()->attach($logo1->id);
        $group->save();

        $logo2 = factory(Logo::class)->create();
        $childGroup->logos()->attach($logo2->id);
        $childGroup->save();

        $this->assertTrue($user->canUseLogo($logo1));
        $this->assertFalse($user->canUseLogo($logo2));
    }

    public function testCanManageLogo__admin()
    {
        $user  = factory(User::class)->create(['super_admin' => false]);
        $group = factory(Group::class)->create();
        $user->roles()
             ->save(factory(Role::class)->make([
                 'admin'    => true,
                 'group_id' => $group->id,
             ]));
        $childGroup = factory(Group::class)->create(['parent_id' => $group->id]);
        $logo       = factory(Logo::class)->create();
        $childGroup->logos()->attach($logo->id);
        $childGroup->save();

        $this->assertTrue($user->canManageLogo($logo));
    }

    public function testCanManageLogo__nonAdmin()
    {
        $user  = factory(User::class)->create(['super_admin' => false]);
        $group = factory(Group::class)->create();
        $user->roles()
             ->save(factory(Role::class)->make([
                 'admin'    => false,
                 'group_id' => $group->id,
             ]));
        $logo = factory(Logo::class)->create();
        $group->logos()->attach($logo->id);
        $group->save();

        $this->assertFalse($user->canManageLogo($logo));
    }
}
