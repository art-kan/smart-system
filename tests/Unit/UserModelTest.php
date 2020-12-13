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

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function testRelationWithOrganization()
    {
        /** @var Organization $organization */
        $organization = Organization::factory()->create();

        /** @var User $user */
        $user = User::factory(['organization_id' => $organization->id])->create();

        self::assertEquals($organization->id, $user->organization->id);
    }

    public function testRelationWithGroups()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Collection|Group[] $groups */
        $groups = Group::factory(5)->create();

        DB::table('group_lists')->where('user_id', $user->id)->delete();
        DB::table('group_lists')
            ->insert(
                $groups->map(function ($group) use ($user) {
                    return [
                        'group_id' => $group->id,
                        'user_id' => $user->id,
                    ];
                })->toArray()
            );

        self::assertEquals(
            Arr::sortRecursive($user->groups->pluck('id')->toArray()),
            Arr::sortRecursive($groups->pluck('id')->toArray())
        );
    }

    public function testRelationWithPrimitiveGroup()
    {
        /** @var User $user */
        $user = User::factory()->create();
        DB::table('groups')->where('only_user_id', $user->id)->delete();

        /** @var Group $group */
        $group = Group::factory(['only_user_id' => $user->id])->create();

        self::assertEquals($group->id, $user->primitiveGroup->id);
    }
}
