<?php

namespace App\Console\Commands;

use App\Constants;
use App\Traits\ValidationTrait;
use Illuminate\Console\Command;

class flashcardInteractive extends Command
{
    use ValidationTrait;
    protected $signature = 'flashcard:interactive';

    protected $description = 'Interactive CLI program for Flashcard practice';

    public function handle(): void
    {
        $email = $this->askWithValidation(Constants::EMAIL_PROMPT, Constants::EMAIL);
        $this->displayMainMenu($email);
    }

    private function displayMainMenu(string $email): void
    {
        $this->info(Constants::WELCOME_MESSAGE);

        do {
            $menuOptions = Constants::MENU_OPTIONS;

            $menu = "\n+----------------------------+\n";
            $menu .= "|        Flashcard CLI       |\n";
            $menu .= "+----------------------------+\n";

            foreach ($menuOptions as $option) {
                $menu .= "| " . str_pad($option, 26) . "|\n";
            }

            $menu .= "+----------------------------+\n";

            $this->info($menu);

            $choice = $this->ask(Constants::CHOOSE_OPTION_PROMPT);
            switch ($choice) {
                case 1:
                    $this->createFlashcard();
                    break;
                case 2:
                    $this->listFlashcards();
                    break;
                case 3:
                    $this->practice($email);
                    break;
                case 4:
                    $this->progress($email);
                    break;
                case 5:
                    $this->reset($email);
                    break;
                case 6:
                    $this->info(Constants::GOODBYE_MESSAGE);
                    break;
                default:
                    $this->error(Constants::INVALID_OPTION_MESSAGE);
            }
        } while ($choice != 6);
    }
}
