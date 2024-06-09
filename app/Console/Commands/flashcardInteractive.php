<?php

namespace App\Console\Commands;

use App\Constants;
use App\Services\FlashcardService;
use App\Traits\ValidationTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class flashcardInteractive extends Command
{
    use ValidationTrait;
    protected $signature = 'flashcard:interactive';

    protected $description = 'Interactive CLI program for Flashcard practice';

    public function __construct(private FlashcardService $flashcardService)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $email = $this->askWithValidation(Constants::EMAIL_PROMPT, Constants::EMAIL);
        $this->displayMainMenu($email);
    }

    private function createFlashcard(): void
    {
        $question = $this->askWithValidation(Constants::QUESTION_PROMPT, Constants::QUESTION);
        $answer = $this->askWithValidation(Constants::ANSWER_PROMPT, Constants::ANSWER);

        try {
            $this->flashcardService->createFlashcard($question, $answer);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            $this->info(Constants::FLASHCARD_CREATED_FAIL);

            return;
        }

        $this->info(Constants::FLASHCARD_CREATED_SUCCESS);
    }

    /*@ToDo listFlashcards*/
    /*@ToDo practice*/
    /*@ToDo progress*/

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
