<?php

namespace Database\Factories;

use App\Models\Flashcard;
use Illuminate\Database\Eloquent\Factories\Factory;

class PracticeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'status' => $this->faker->randomElement(['Correct', 'Incorrect']),
            'flashcard_id' => Flashcard::factory(),
            'answer' => $this->faker->word,
        ];
    }
}
