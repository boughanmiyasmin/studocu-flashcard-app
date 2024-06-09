<?php

namespace App\Repositories;

use App\Models\Flashcard;
use Illuminate\Database\Eloquent\Collection;

class FlashcardRepository
{
    public function create(array $data): Flashcard
    {
        return Flashcard::create($data);
    }

    public function getFlashcards(): Collection
    {
        return Flashcard::all();
    }
}
