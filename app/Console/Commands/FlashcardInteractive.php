<?php

namespace App\Console\Commands;

use App\Console\Commands\FlashcardActions\CreateFlashcardAction;
use App\Console\Commands\FlashcardActions\GetEmailAction;
use App\Console\Commands\FlashcardActions\ListFlashcardsAction;
use App\Console\Commands\FlashcardActions\PracticeFlashcardsAction;
use App\Console\Commands\FlashcardActions\ProgressAction;
use App\Console\Commands\FlashcardActions\ResetAction;
use App\Console\Commands\FlashcardActions\ExitAction;
use App\Constants;
use App\Services\FlashcardService;
use App\Services\PracticeService;
use App\Traits\ValidationTrait;
use Illuminate\Console\Command;

class FlashcardInteractive extends Command
{
    use ValidationTrait;

    const CREATE_FLASHCARD_ACTION = 1;
    const LIST_FLASHCARD_ACTION = 2;
    const PRACTICE_ACTION = 3;
    const PROGRESS_ACTION = 4;
    const RESET_ACTION = 5;
    const EXIT_ACTION = 6;
    const MENU_OPTIONS = [
        self::CREATE_FLASHCARD_ACTION . '. Create a flashcard',
        self::LIST_FLASHCARD_ACTION . '. List all flashcards',
        self::PRACTICE_ACTION . '. Practice',
        self::PROGRESS_ACTION . '. Stats',
        self::RESET_ACTION . '. Reset',
        self::EXIT_ACTION . '. Exit',
    ];

    const WELCOME_MESSAGE = 'Welcome to the Flashcard CLI!';
    const CHOOSE_OPTION_PROMPT = 'Choose an option';

    protected $signature = 'flashcard:interactive';
    protected $description = 'Interactive CLI program for Flashcard practice';

    public string $email;
    private array $actions;

    public function __construct(private FlashcardService $flashcardService, private PracticeService $practiceService)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $getEmailAction = new GetEmailAction($this);
        $this->email = $getEmailAction->execute();

        $this->actions = [
            self::CREATE_FLASHCARD_ACTION => new CreateFlashcardAction($this, $this->flashcardService),
            self::LIST_FLASHCARD_ACTION => new ListFlashcardsAction($this, $this->flashcardService),
            self::PRACTICE_ACTION => new PracticeFlashcardsAction($this, $this->practiceService),
            self::PROGRESS_ACTION => new ProgressAction($this, $this->practiceService),
            self::RESET_ACTION => new ResetAction($this, $this->practiceService),
            self::EXIT_ACTION => new ExitAction($this)
        ];
        $this->info(self::WELCOME_MESSAGE);

        do {
            $this->displayMenu();

            $choice = $this->ask(self::CHOOSE_OPTION_PROMPT);

            array_key_exists($choice, $this->actions) ? $this->actions[$choice]->execute() :
                $this->error(Constants::INVALID_OPTION_MESSAGE);
        } while ($choice != self::EXIT_ACTION);
    }

    private function displayMenu(): void
    {
        $menuOptions = self::MENU_OPTIONS;

        $menu = "\n+----------------------------+\n";
        $menu .= "|        Flashcard CLI       |\n";
        $menu .= "+----------------------------+\n";

        foreach ($menuOptions as $option) {
            $menu .= "| " . str_pad($option, 26) . "|\n";
        }

        $menu .= "+----------------------------+\n";
        $this->info($menu);
    }
}
