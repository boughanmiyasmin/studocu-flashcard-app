<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait progressBarTrait
{
    const BAR_UNITS = 50;
    const MAX_PERCENTAGE = 100;
    const PERCENTAGE = [
        'completionPercentage' => 'Percentage of completion: ',
        'correctAnswersPercentage' => 'Percentage of correct answers: '
    ];
    private function buildProgressBar(Collection $practiceFlashcards, string $type): void
    {
        $completionPercentage = $this->practiceService->{$type}($practiceFlashcards);
        $this->command->warn(self::PERCENTAGE[$type]);
        $this->displayProgressBar($completionPercentage);
    }

    private function displayProgressBar(int $percentage): void
    {
        $percentage = max(0, min(self::MAX_PERCENTAGE, $percentage));

        $completed = round($percentage / 2); //dividing the percentage by 2 converts a 0-100 percentage into a 0-50 scale.
        $remaining = self::BAR_UNITS - $completed;

        $bar = str_repeat('█', $completed) . str_repeat('░', $remaining);

        echo sprintf("%d%% [%s] %d%%\n", $percentage, $bar, self::MAX_PERCENTAGE);
    }
}
