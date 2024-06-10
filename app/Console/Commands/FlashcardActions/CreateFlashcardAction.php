<?php

namespace App\Console\Commands\FlashcardActions;

use App\Console\Commands\FlashcardInteractive;
use App\Services\FlashcardService;
use App\Traits\ValidationTrait;
use Illuminate\Support\Facades\Log;

class CreateFlashcardAction implements MenuActionInterface
{
    use ValidationTrait;

    const QUESTION_PROMPT = 'Enter the flashcard question';
    const QUESTION = 'question';
    const ANSWER_PROMPT = 'Enter the answer';
    const ANSWER = 'answer';
    const FLASHCARD_CREATED_FAIL = 'Something Went wrong while creating the flashcard, please try again later!';
    const FLASHCARD_CREATED_SUCCESS = 'Flashcard created successfully!';

    public function __construct(private FlashcardInteractive $command, private FlashcardService $flashcardService)
    {
    }

    public function execute(): void
    {
        $question = $this->askWithValidation(self::QUESTION_PROMPT, self::QUESTION);
        $answer = $this->askWithValidation(self::ANSWER_PROMPT, self::ANSWER);

        try {
            $this->flashcardService->createFlashcard($question, $answer);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            $this->command->info(self::FLASHCARD_CREATED_FAIL);

            return;
        }

        $this->command->info(self::FLASHCARD_CREATED_SUCCESS);
    }
}
