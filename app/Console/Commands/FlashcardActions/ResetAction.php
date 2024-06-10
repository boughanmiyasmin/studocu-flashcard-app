<?php

namespace App\Console\Commands\FlashcardActions;

use App\Console\Commands\FlashcardInteractive;
use App\Services\PracticeService;

class ResetAction implements MenuActionInterface
{
    const RESET_SUCCESS = 'Progress has been reset.';
    const RESET_FAIL = 'Something Went wrong while resetting your progress, please try again later!';

    public function __construct(private FlashcardInteractive $command, private PracticeService $practiceService)
    {
    }

    public function execute(): void
    {
        !$this->practiceService->resetProgress($this->command->email) ? $this->command->info(self::RESET_FAIL) :
            $this->command->info(self::RESET_SUCCESS);
    }
}
