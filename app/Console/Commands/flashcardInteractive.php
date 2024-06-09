<?php

namespace App\Console\Commands;

use App\Constants;
use App\Models\Flashcard as flashcardModel;
use App\Services\FlashcardService;
use App\Services\PracticeService;
use App\Traits\progressBarTrait;
use App\Traits\ValidationTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class flashcardInteractive extends Command
{
    use ValidationTrait;
    use progressBarTrait;

    protected $signature = 'flashcard:interactive';

    protected $description = 'Interactive CLI program for Flashcard practice';

    public function __construct(private FlashcardService $flashcardService, private PracticeService $practiceService)
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

    private function listFlashcards(): void
    {
        $flashcards = $this->flashcardService->listFlashcards();
        $this->table(Constants::LIST_HEADER, $flashcards->toArray());
    }

    private function practice(string $email): void
    {
        do {
            $practiceFlashcards = $this->practiceService->practiceFlashcards($email);

            $this->table(Constants::PRACTICE_LIST_HEADER, $practiceFlashcards->toArray());

            $this->buildProgressBar($practiceFlashcards, Constants::COMPLETION_PERCENTAGE);

            $choice = $this->ask(Constants::ENTER_FLASHCARD_ID_PROMPT);

            if (strtolower($choice) === Constants::EXIT) {
                $this->info(Constants::ENDING_PRACTICE);
                continue;
            }

            $flashcard = $this->getUserPracticeFlashcard($choice, $email);
            if (!$flashcard) {
                continue;
            }

            $answer = $this->askWithValidation($flashcard->question, Constants::ANSWER);

            $status = $this->practiceService->processAnswer($flashcard, $email, $answer);

            $this->info($status . '!');

        } while (strtolower($choice) !== Constants::EXIT);
    }

    private function getUserPracticeFlashcard(string $choice, string $email): flashcardModel|bool
    {
        try {
            $flashcard = $this->practiceService->practiceFlashcard($choice, $email);
            if ($flashcard->status === Constants::CORRECT) {
                $this->info(Constants::ALREADY_CORRECT_MESSAGE);

                return false;
            }
        } catch (\Exception $e) {
            $this->info($e->getMessage());

            return false;
        }

        return $flashcard;
    }

    private function progress(string $email): void
    {
        $practiceFlashcards = $this->practiceService->practiceFlashcards($email);
        $flashcardsCount = $practiceFlashcards->count();

        $this->info(Constants::FLASHCARD_COUNT . $flashcardsCount);
        $this->buildProgressBar($practiceFlashcards, Constants::COMPLETION_PERCENTAGE);
        $this->buildProgressBar($practiceFlashcards, Constants::CORRECT_PERCENTAGE);
    }

    private function reset(string $email): void
    {
        !$this->practiceService->resetProgress($email) ? $this->info(Constants::RESET_FAIL) : $this->info(
            Constants::RESET_SUCCESS
        );
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
