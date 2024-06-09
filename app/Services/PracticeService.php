<?php

namespace App\Services;

use App\Constants;
use App\Models\Flashcard;
use App\Models\Practice;
use App\Repositories\PracticeRepository;
use Illuminate\Support\Collection;

class PracticeService
{
    public function __construct(private PracticeRepository $practiceRepository)
    {
    }
    public function practiceFlashcards(string $email): Collection
    {
        $practiceFlashcards = $this->practiceRepository->getPracticeFlashcards($email);

        return $this->buildTableData($practiceFlashcards);
    }

    private function buildTableData(Collection $practiceFlashcards): Collection
    {
        return $practiceFlashcards->map(function (Flashcard $flashcard) {
            $flashcard->determinePracticeStatus();

            return $flashcard->getPracticeProperties();
        });
    }

    public function practiceFlashcard(string $id, string $email): Flashcard
    {
        /**@var Flashcard $flashcard */
        $flashcard = $this->practiceRepository->getPracticeFlashcard($id, $email);

        if (!$flashcard) {
            throw new \Exception(Constants::INVALID_OPTION_MESSAGE);
        }

        return $flashcard->determinePracticeStatus();
    }

    public function processAnswer(Flashcard $flashcard, string $email, string $answer): string
    {
        $practice = $flashcard->getPractice();
        $status = trim($answer) === $flashcard->answer ? Constants::CORRECT : Constants::INCORRECT;

        $data = [
            'email' => $email,
            'status' => $status,
            'flashcard_id' => $flashcard->id,
            'answer' => $answer
        ];

        $practice instanceof Practice ? $this->practiceRepository->update($practice, $data) :
            $this->practiceRepository->create($data);

        return $status;
    }
}
