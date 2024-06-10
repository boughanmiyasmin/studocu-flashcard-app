<?php

namespace App\Console\Commands\FlashcardActions;

use App\Console\Commands\FlashcardInteractive;

class ExitAction implements MenuActionInterface
{
    const GOODBYE_MESSAGE = 'Goodbye!';

    public function __construct(private FlashcardInteractive $command)
    {
    }

    public function execute(): void
    {
        $this->command->info(self::GOODBYE_MESSAGE);
    }
}
