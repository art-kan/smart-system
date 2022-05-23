<?php

namespace Database\Factories;

use App\Models\ReportRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class ReportRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReportRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'body' => $this->faker->text,
            'title' => $this->faker->title,
            'status' => $this->faker->randomElement(ReportRequest::STATUSES),
            'report_request_id' => function () {
                return ReportRequest::all('id')->random()->id;
            },
            'created_by' => function () {
                return User::all('id')->random()->id;
            },
            'document_set_id' => function () {
                return DB::table('document_sets')->insertGetId([]);
            },
        ];
    }
}
