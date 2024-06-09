<?php

namespace App\Repositories;

use App\Constants;
use App\Models\Flashcard;
use App\Models\Practice;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class PracticeRepository
{
    public function getPracticeFlashcards(string $email): Collection
    {
        return Flashcard::with([
            'practices' => function ($query) use ($email) {
                $query->where('email', $email);
            }
        ])
        ->get();
    }

    public function getPracticeFlashcard(string $flashcardId, string $email): Model|null
    {
        return Flashcard::with([
            'practices' => function ($query) use ($email) {
                $query->where('email', $email);
            }
        ])
            ->where('id', $flashcardId)
            ->first();
    }

    public function create(array $data): Practice
    {
        return Practice::create($data);
    }

    public function update(Practice$practice, array $data): void
    {
        Practice::where('id', $practice->id)->update($data);
    }

    public function getAnsweredQuestionsCount(Collection $flashcards): int
    {
        return $flashcards->whereIn('Status',[Constants::CORRECT, Constants::INCORRECT])
            ->count();
    }

    public function resetProgress(string $email): bool
    {
        return Practice::where('email', $email)->update(['status' => Constants::NOT_ANSWERED]);
    }
}
