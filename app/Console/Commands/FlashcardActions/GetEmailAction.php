<?php

namespace App\Console\Commands\FlashcardActions;

use App\Console\Commands\FlashcardInteractive;
use App\Traits\ValidationTrait;

class GetEmailAction
{
    use ValidationTrait;

    const EMAIL_PROMPT = 'Enter your email';
    const EMAIL = 'email';

    public function __construct(private FlashcardInteractive $command)
    {
    }

    public function execute(): string
    {
        return $this->askWithValidation(self::EMAIL_PROMPT, self::EMAIL);
    }
}
