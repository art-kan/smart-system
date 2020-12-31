<?php

namespace Database\Seeders;

use App\Extra\Privileges\Privilege;
use App\Models\ArchiveDocument;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\DocumentSet;
use App\Models\Group;
use App\Models\Organization;
use App\Models\Report;
use App\Models\ReportRequest;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithFaker;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->faker = Factory::create();
        $n = 3;
        while ($n--) $this->seedOrganization();

        $this->seedDDoPEWithSchools();
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

    private function seedDDoPEWithSchools()
    {
        /** @var Organization $organization */
        $organization = Organization::factory()->create();

        /** @var User $dd_o_pe */
        $dd_o_pe = User::create([
            'name' => 'Raino',
            'role' => 'Raino',
            'email' => 'raino@example.com',
            'password' => Hash::make('password'),
            'organization_id' => $organization->id,
        ]);

        $schools = Group::create([
            'name' => 'schools',
            'organization_id' => $organization->id,
        ]);

        /** @var User[] $users */
        $users = [];
        /** @var Chat[] $chats */
        $chats = [];

        foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15] as $n) {
            $user = User::create([
                'name' => $n,
                'role' => 'School',
                'email' => "school$n@example.com",
                'password' => Hash::make('password'),
                'organization_id' => $organization->id,
            ]);

            $users[] = $user;

            $chat = Chat::create(['is_private' => true]);
            $chats[] = $chat;
            DB::table('private_chat_links')->insert([
                ['user_id' => $user->id, 'to_user_id' => $dd_o_pe->id, 'chat_id' => $chat->id],
                ['user_id' => $dd_o_pe->id, 'to_user_id' => $user->id, 'chat_id' => $chat->id],
            ]);
        }

        $schools->users()->attach(Arr::pluck($users, 'id'));

        $dd_o_pe->updatePrivilege($schools, Privilege::getGroupCreatorDefaultPriv());

        $set = DocumentSet::create();
        $set->documents()->attach(ArchiveDocument::create([
            'filename' => 'sample.xlsx',
            'size' => 434424,
            'path' => 'archive/Sample.xlsx',
        ]));

        $set->documents()->attach(ArchiveDocument::create([
            'filename' => 'sample2.xlsx',
            'size' => 201,
            'path' => 'archive/Sample.xlsx',
        ]));

        $request = ReportRequest::create([
            'created_by' => $dd_o_pe->id,
            'title' => 'Пример названия отчет-запроса',
            'body' => '<p>Пример описания отчет-запроса.</p>'
                . '<p><i>Курсивный</i> <b>Жирный</b> <i><b>Курсивый и жирный</b></i></p>',
            'document_set_id' => $set->id,
            'created_at' => Carbon::create('yesterday')->toDateTime(),
        ]);

        for ($i = 3; $i >= 0; $i--) {
            /** @var User $school */
            $school = $this->faker->randomElement($users);

            $set = DocumentSet::create();
            $set->documents()->attach(ArchiveDocument::create([
                'filename' => 'sample.xlsx',
                'size' => 434424,
                'path' => 'archive/Sample.xlsx',
            ]));

            $set->documents()->attach(ArchiveDocument::create([
                'filename' => 'sample2.xlsx',
                'size' => 201,
                'path' => 'archive/Sample.xlsx',
            ]));

            Report::factory([
                'created_by' => $school->id,
                'report_request_id' => $request->id,
                'status' => Report::DEFAULT_STATE,
                'document_set_id' => $set->id,
            ])->create();
        }

        ChatMessage::factory([
            'chat_id' => $chats[0]->id,
            'sent_by'=> $dd_o_pe->id,
            'body' => 'Здравствуйте ждем ваш отчет на протяжении недели, когда сдадите?'])->create();

        ChatMessage::factory([
            'chat_id' => $chats[0]->id,
            'sent_by'=> $users[0]->id,
            'body' => 'Мы отправили отчет вам вчера вечером'])->create();
    }
}
