<?php

namespace App\Traits;

use App\Constants;
use Illuminate\Support\Collection;

trait progressBarTrait
{
    private function buildProgressBar(Collection $practiceFlashcards, string $type): void
    {
        $completionPercentage = $this->practiceService->{$type}($practiceFlashcards);
        $this->warn(Constants::PERCENTAGE[$type]);
        $this->displayProgressBar($completionPercentage);
    }

    private function displayProgressBar(int $percentage): void
    {
        $percentage = max(0, min(100, $percentage));

        $completed = round($percentage / 2);
        $remaining = 50 - $completed;

        $bar = str_repeat('█', $completed) . str_repeat('░', $remaining);

        $this->output->writeln(sprintf("%d%% [%s] %d%%", $percentage, $bar, $percentage));

    }
}
