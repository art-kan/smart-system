<?php

namespace Tests\Unit;

use App\Models\Group;
use App\Models\Organization;
use App\Models\User;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

class OrganizationModelTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function testRelationWithSuperGroup()
    {
        /** @var Organization $organization */
        $organization = Organization::factory()->create();
        /** @var Group $group */
        $group = Group::factory()->create();

        $organization->super_group_id = $group->id;
        $organization->save();

        self::assertEquals($group->id, $organization->superGroup->id);
    }

    public function testRelationWithStaffGroup()
    {
        /** @var Organization $organization */
        $organization = Organization::factory()->create();
        /** @var Group $group */
        $group = Group::factory()->create();

        $organization->staff_group_id = $group->id;
        $organization->save();

        self::assertEquals($group->id, $organization->staffGroup->id);
    }

    public function testRelationWithGroups()
    {
        /** @var Organization $organization */
        $organization = Organization::factory()->create();
        DB::table('groups')->delete();

        /** @var Collection|Group[] $groups */
        $groups = Group::factory(['organization_id' => $organization->id])->count(5)->create();

        self::assertEquals(
            Arr::sortRecursive($groups->pluck('id')->toArray()),
            Arr::sortRecursive($organization->groups->pluck('id')->toArray())
        );
    }

    public function testRelationWithUsers()
    {
        /** @var Organization $organization */
        $organization = Organization::factory()->create();
        DB::table('users')->delete();

        /** @var Collection|User[] $users */
        $users = User::factory(['organization_id' => $organization->id])->count(5)->create();

        self::assertEquals(
            Arr::sortRecursive($users->pluck('id')->toArray()),
            Arr::sortRecursive($organization->members->pluck('id')->toArray())
        );
    }
}
