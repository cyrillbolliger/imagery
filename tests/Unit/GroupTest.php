<?php

namespace App;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(\RootSeeder::class);
    }

    public function testIsDescendantOf__grandparent()
    {
        $root       = Group::first();
        $child      = factory(Group::class)->create(['parent_id' => $root->id]);
        $grandchild = factory(Group::class)->create(['parent_id' => $child->id]);

        $this->assertTrue($grandchild->isDescendantOf($root));
    }

    public function testIsDescendantOf__stranger()
    {
        $root       = Group::first();
        $child      = factory(Group::class)->create(['parent_id' => $root->id]);
        $grandchild = factory(Group::class)->create(['parent_id' => $child->id]);

        $this->assertFalse($grandchild->isDescendantOf(0));
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

    public function testChildren()
    {
        $root       = factory(Group::class)->create(['name' => 'root']);
        $child1     = factory(Group::class)->create([
            'name'      => 'child1',
            'parent_id' => $root->id
        ]);
        $child2     = factory(Group::class)->create([
            'name'      => 'child2',
            'parent_id' => $root->id
        ]);
        $grandchild = factory(Group::class)->create([
            'name'      => 'grandchild',
            'parent_id' => $child1->id
        ]);

        $children = $root->children;

        $this->assertFalse($children->contains($root));
        $this->assertTrue($children->contains($child1));
        $this->assertTrue($children->contains($child2));
        $this->assertFalse($children->contains($grandchild));
    }

    public function testDescendants()
    {
        $root       = factory(Group::class)->create(['name' => 'root']);
        $child1     = factory(Group::class)->create([
            'name'      => 'child1',
            'parent_id' => $root->id
        ]);
        $child2     = factory(Group::class)->create([
            'name'      => 'child2',
            'parent_id' => $root->id
        ]);
        $grandchild = factory(Group::class)->create([
            'name'      => 'grandchild',
            'parent_id' => $child1->id
        ]);

        $children = collect($root->descendants->toArray());

        $this->assertEmpty($children->where('id', $root->id));
        $this->assertNotEmpty($children->where('id', $child1->id));
        $this->assertNotEmpty($children->where('id', $child2->id));
        $this->assertNotEmpty(
            collect($children->where('id', $child1->id)->first()['descendants'])->where('id', $grandchild->id)
        );
    }

    public function testWithDescendants()
    {
        $root       = factory(Group::class)->create(['name' => 'root']);
        $child1     = factory(Group::class)->create([
            'name'      => 'child1',
            'parent_id' => $root->id
        ]);
        $child2     = factory(Group::class)->create([
            'name'      => 'child2',
            'parent_id' => $root->id
        ]);
        $grandchild = factory(Group::class)->create([
            'name'      => 'grandchild',
            'parent_id' => $child1->id
        ]);

        $rootWithChildren = collect(Group::with('descendants')
                                         ->where('id', $root->id)
                                         ->get()
                                         ->toArray());

        // root
        $this->assertEquals(1, $rootWithChildren->count());
        $this->assertNotEmpty($rootWithChildren->where('id', $root->id));

        // children
        $this->assertEquals(2, collect(
            $rootWithChildren->where('id', $root->id)->first()['descendants']
        )->count());
        $this->assertNotEmpty(collect(
            $rootWithChildren->where('id', $root->id)->first()['descendants']
        )->where('id', $child1->id));
        $this->assertNotEmpty(collect(
            $rootWithChildren->where('id', $root->id)->first()['descendants']
        )->where('id', $child2->id));

        // grandchild
        $this->assertEquals(1, count(
            collect(
                $rootWithChildren->where('id', $root->id)->first()['descendants']
            )->where('id', $child1->id)->first()['descendants']));
        $this->assertNotEmpty(
            collect(
                collect(
                    $rootWithChildren->where('id', $root->id)->first()['descendants']
                )->where('id', $child1->id)->first()['descendants']
            )->where('id', $grandchild->id));
    }
}
