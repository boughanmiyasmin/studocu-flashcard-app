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

            $validationRule = ValidationRules::{$field . "Rule"}();
            $rules = $validationRule['rules'];
            $messages = $validationRule['messages'];

            $validator = Validator::make([$field => $value], $rules, $messages);
            if ($validator->fails()) {
                $this->error($validator->errors()->first($field));
            }

        } while ($validator->fails());

        return $value;
    }
}
