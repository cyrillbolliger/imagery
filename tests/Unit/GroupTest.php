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
}
