<?php

namespace App\Console\Commands\FlashcardActions;

use App\Console\Commands\FlashcardInteractive;
use App\Services\PracticeService;
use App\Traits\progressBarTrait;

class ProgressAction implements MenuActionInterface
{
    use progressBarTrait;

    const COMPLETION_PERCENTAGE = 'completionPercentage';
    const CORRECT_PERCENTAGE = 'correctAnswersPercentage';
    const FLASHCARD_COUNT = 'The total amount of questions: ';

    public function __construct(private FlashcardInteractive $command, private PracticeService $practiceService)
    {
    }

    public function execute(): void
    {
        $practiceFlashcards = $this->practiceService->practiceFlashcards($this->command->email);
        $flashcardsCount = $practiceFlashcards->count();

        $this->command->info(self::FLASHCARD_COUNT . $flashcardsCount);
        $this->buildProgressBar($practiceFlashcards, self::COMPLETION_PERCENTAGE);
        $this->buildProgressBar($practiceFlashcards, self::CORRECT_PERCENTAGE);
    }
}
