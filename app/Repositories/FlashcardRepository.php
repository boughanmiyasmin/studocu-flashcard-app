<?php

namespace App\Repositories;

use App\Models\Flashcard;

class FlashcardRepository
{
    public function create(array $data): Flashcard
    {
        return Flashcard::create($data);
    }
}
