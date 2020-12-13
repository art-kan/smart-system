<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Group::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $last_organization = Organization::latest()->first();

        return [
            'organization_id' => $last_organization ? $last_organization->id : Organization::factory(),
            'name' => 'group #'.$this->faker->numberBetween(),
            'description' => $this->faker->boolean ? $this->faker->text : null,
            'password' => $this->faker->boolean ? $this->faker->password : null,
            'is_hidden' => $this->faker->boolean(70),
        ];
    }
}
