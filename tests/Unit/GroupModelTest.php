<?php

namespace Tests\Unit;

use App\Models\Group;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GroupModelTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function testRelationWithUsers()
    {
        /** @var Group $group */
        $group = Group::factory()->create();

        /** @var Collection|User[] $users */
        $users = User::factory(5)->create();

        DB::table('group_lists')->delete();
        DB::table('group_lists')->insert(
            $users->map(function ($user) use ($group) {
                return ['user_id' => $user->id, 'group_id' => $group->id];
            })->toArray()
        );

        self::assertEquals(
            Arr::sortRecursive($users->pluck('id')->toArray()),
            Arr::sortRecursive($group->users->pluck('id')->toArray())
        );
    }

    public function testRelationWithOrganization()
    {
        /** @var Organization $organization */
        $organization = Organization::factory()->create();

        /** @var Group $group */
        $group = Group::factory(['organization_id' => $organization->id])->create();

        self::assertEquals($organization->id, $group->organization->id);
    }
}
