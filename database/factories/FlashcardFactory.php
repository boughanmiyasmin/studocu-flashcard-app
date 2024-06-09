<?php

namespace Database\Factories;

use App\Models\Flashcard;
use Illuminate\Database\Eloquent\Factories\Factory;

class FlashcardFactory extends Factory
{
    protected $model = Flashcard::class;

    public function definition()
    {
        return [
            'question' => $this->faker->sentence,
            'answer' => $this->faker->word,
        ];
    }
}
