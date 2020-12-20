<?php

namespace Database\Factories;

use App\Models\ArchiveDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArchiveDocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ArchiveDocument::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $ext = $this->faker->fileExtension;
        return [
            'filename' => $this->faker->word . $ext,
            'path' => $this->faker->file('/tmp', '/tmp', true),
        ];
    }
}
