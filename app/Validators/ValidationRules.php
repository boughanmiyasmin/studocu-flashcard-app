<?php

namespace App\Validators;

class ValidationRules
{
    public static function emailRule(): array
    {
        return [
            'email' => [
                'required',
                'email',
            ]
        ];
    }
}
