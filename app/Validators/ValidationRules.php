<?php

namespace App\Validators;

class ValidationRules
{
    public static function emailRule(): array
    {
        return [
            'rules' => [
                'email' => [
                    'required',
                    'email',
                ]
            ],
            'messages' => [
                'email.required' => 'The email field is required.',
                'email.email' => 'The email must be a valid email address.',
            ]
        ];
    }

    public static function questionRule(): array

    {
        return [
            'rules' => [
                'question' => [
                    'required',
                    'string',
                    'unique:flashcards,question',
                ]
            ],
            'messages' => [
                'question.required' => 'The question field is required.',
                'question.string' => 'The question must be a string.',
                'question.unique' => 'This question already exists.',
            ]
        ];
    }


    public static function answerRule(): array
    {
        return [
            'rules' => [
                'answer' => [
                    'required',
                    'string',
                ]
            ],
            'messages' => [
                'answer.required' => 'The answer field is required.',
                'answer.string' => 'The answer must be a string.',
            ]
        ];
    }
}
