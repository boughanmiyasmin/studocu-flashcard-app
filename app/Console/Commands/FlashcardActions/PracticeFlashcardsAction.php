<?php

namespace App\Console\Commands\FlashcardActions;

use App\Console\Commands\FlashcardInteractive;
use App\Constants;
use App\Models\Flashcard as flashcardModel;
use App\Services\PracticeService;
use App\Traits\progressBarTrait;
use App\Traits\ValidationTrait;

class PracticeFlashcardsAction implements MenuActionInterface
{
    use progressBarTrait;
    use ValidationTrait;

    const PRACTICE_LIST_HEADER = ['ID', 'Question', 'Status'];
    const ENTER_FLASHCARD_ID_PROMPT = 'Enter the ID of the flashcard you want to practice or type exit to go back to the main menu';
    const ALREADY_CORRECT_MESSAGE = 'You have already answered this question correctly.';
    const EXIT = 'exit';
    const ANSWER = 'answer';
    const ENDING_PRACTICE = 'Ending practice...';
    const COMPLETION_PERCENTAGE = 'completionPercentage';

    public function __construct(private FlashcardInteractive $command, private PracticeService $practiceService)
    {
    }

    public function execute(): void
    {
        do {
            $practiceFlashcards = $this->practiceService->practiceFlashcards($this->command->email);
            $this->command->table(self::PRACTICE_LIST_HEADER, $practiceFlashcards->toArray());

            $this->buildProgressBar($practiceFlashcards, self::COMPLETION_PERCENTAGE);

            $choice = $this->command->ask(self::ENTER_FLASHCARD_ID_PROMPT);
            if (!$choice) {
                $this->command->error(Constants::INVALID_OPTION_MESSAGE);
                continue;
            }

            if (strtolower($choice) === self::EXIT) {
                $this->command->info(self::ENDING_PRACTICE);
                continue;
            }

            $flashcard = $this->getUserPracticeFlashcard($choice);
            if (!$flashcard) {
                continue;
            }

            if ($flashcard->status === Constants::CORRECT) {
                $this->command->info(self::ALREADY_CORRECT_MESSAGE);
                continue;
            }

            $answer = $this->askWithValidation($flashcard->question, self::ANSWER);
            $status = $this->practiceService->processAnswer($flashcard, $this->command->email, $answer);
            $this->command->info($status . '!');
        } while (strtolower($choice) !== self::EXIT);
    }

    private function getUserPracticeFlashcard(string $choice): flashcardModel|bool
    {
        try {
            $flashcard = $this->practiceService->practiceFlashcard($choice, $this->command->email);
        } catch (\Exception $e) {
            $this->command->info($e->getMessage());

            return false;
        }

        return $flashcard;
    }
}
