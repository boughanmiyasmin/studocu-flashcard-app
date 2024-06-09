<?php

namespace App\Services;

use App\Models\Flashcard;
use App\Repositories\FlashcardRepository;
use Illuminate\Database\Eloquent\Collection;

class FlashcardService
{
    public function __construct(protected FlashcardRepository $flashcardRepository)
    {
    }

    public function createFlashcard(string $question, string $answer): Flashcard
    {
        $data = [
            'question' => trim($question),
            'answer' => trim($answer)
        ];

        return $this->flashcardRepository->create($data);
    }

    public function listFlashcards(): Collection
    {
        return $this->flashcardRepository->getFlashcards();
    }
}
