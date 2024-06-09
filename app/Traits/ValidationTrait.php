<?php

namespace App\Traits;

use App\Constants;
use App\Validators\ValidationRules;
use Illuminate\Support\Facades\Validator;

trait ValidationTrait
{
    public function askWithValidation(string $question, string $field): string
    {
        do {
            $value = $this->ask($question);

            $validator = Validator::make([$field => $value], ValidationRules::{$field . "Rule"}());
            if ($validator->fails()) {
                $this->error(Constants::INVALID_OPTION_MESSAGE);
            }

        } while ($validator->fails());

        return $value;
    }
}
