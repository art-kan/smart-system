<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Organization;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $n = 3;
        while ($n--) $this->seedOrganization();
    }

    public function seedOrganization()
    {
        $faker = Factory::create();

        Organization::factory()->create();

        /** @var Group $super_group */
        $super_group = Group::factory()->create();

        /** @var Group $staff_group */
        $staff_group = Group::factory()->create();

        /** @var Collection|User[] $super_users */
        $super_users = User::factory(2)->create();

        /** @var Collection|User[] $staff_users */
        $staff_users = User::factory(20)->create();

        DB::table('group_lists')->insert(
            $super_users->map(function ($user) use ($super_group) {
                return ['user_id' => $user->id, 'group_id' => $super_group->id];
            })->toArray()
        );

        DB::table('group_lists')->insert(
            $staff_users->map(function ($user) use ($staff_group) {
                return ['user_id' => $user->id, 'group_id' => $staff_group->id];
            })->toArray()
        );

        /** @var Collection|Group[] $groups */
        $groups = Group::factory(5)->create();

        foreach ($groups as $group) {
            $member_ids = [];
            for ($n = $faker->numberBetween(1, 10); $n >= 0; $n--) {
                $member_ids[] = $faker->randomElement($staff_users->toArray())['id'];
            }

            $member_ids = array_unique($member_ids);

            DB::table('group_lists')->insert(
                array_map(function ($member_id) use ($group) {
                    return ['user_id' => $member_id, 'group_id' => $group->id];
                }, $member_ids)
            );
        }
    }
}
