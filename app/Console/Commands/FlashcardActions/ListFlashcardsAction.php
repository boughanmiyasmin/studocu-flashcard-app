<?php

namespace App\Console\Commands\FlashcardActions;

use App\Console\Commands\FlashcardInteractive;
use App\Services\FlashcardService;

class ListFlashcardsAction implements MenuActionInterface
{
    const LIST_HEADER = ['ID', 'Question', 'Answer'];

    public function __construct(private FlashcardInteractive $command, private FlashcardService $flashcardService)
    {
    }

    public function execute(): void
    {
        $flashcards = $this->flashcardService->listFlashcards();
        $this->command->table(self::LIST_HEADER, $flashcards->toArray());
    }
}
