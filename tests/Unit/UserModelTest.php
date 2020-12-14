<?php

namespace Tests\Unit;

use App\Extra\Privileges\Privilege;
use App\Models\Group;
use App\Models\Organization;
use App\Models\User;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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

    public function testCheckPrivilege()
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Collection|Group[] $groups */
        $groups = Group::factory(5)->create();

        DB::table('group_lists')->insert(
            $groups->map(function ($group) use ($user) {
                return ['user_id' => $user->id, 'group_id' => $group['id']];
            })->toArray()
        );

        /** @var Collection|Group[] $target_groups_1 */
        $target_groups_1 = Group::factory(5)->create();

        /** @var Collection|Group[] $target_groups_2 */
        $target_groups_2 = Group::factory(5)->create();

        DB::table(Privilege::getTableNameByTargetType('group'))
            ->insert(
                $target_groups_1->map(function ($group) use ($groups) {
                    return [
                        'group_id' => $this->faker->randomElement($groups->toArray())['id'],
                        'target_id' => $group->id,
                        'inspect_priv' => true,
                    ];
                })->toArray()
            );

        DB::table(Privilege::getTableNameByTargetType('group'))
            ->insert(
                $target_groups_2->map(function ($group) use ($groups) {
                    return [
                        'group_id' => $this->faker->randomElement($groups->toArray())['id'],
                        'target_id' => $group->id,
                        'edit_info_priv' => true,
                    ];
                })->toArray()
            );

        self::assertTrue(
            $user->hasPrivilege($this->faker->randomElement($target_groups_1->toArray())['id'],
                new Privilege('group', ['inspect_priv' => true]))
        );

        self::assertFalse(
            $user->hasPrivilege($this->faker->randomElement($target_groups_2->toArray())['id'],
                new Privilege('group', ['inspect_priv' => true]))
        );

        self::assertTrue(
            $user->hasPrivilege($this->faker->randomElement($target_groups_2->toArray())['id'],
                new Privilege('group', ['edit_info_priv' => true]))
        );

        self::assertFalse(
            $user->hasPrivilege($this->faker->randomElement($target_groups_1->toArray())['id'],
                new Privilege('group', ['edit_info_priv' => true]))
        );
    }
}
