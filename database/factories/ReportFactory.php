<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\ReportRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class ReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Report::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'body' => '<p>'.$this->faker->text(500).'</p>',
            'status' => $this->faker->randomElement(Report::STATUSES),
            'report_request_id' => ReportRequest::factory(),
            'document_set_id' => function () {
                return DB::table('document_sets')->insertGetId([]);
            },
            'created_by' => function () {
                return User::all('id')->random()->id;
            },
        ];
    }
}
