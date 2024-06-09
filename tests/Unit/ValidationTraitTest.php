<?php

namespace Tests\Unit;

use App\Console\Commands\FlashcardInteractive;
use App\Constants;
use App\Validators\ValidationRules;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Mockery;
use Tests\TestCase;

class ValidationTraitTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testAskWithValidationSuccess()
    {
        $rules = ['required', 'email'];
        $this->mock(ValidationRules::class, function ($mock) use ($rules) {
            $mock->shouldReceive('emailRule')->andReturn($rules);
        });

        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock('Illuminate\Contracts\Validation\Validator', function ($mock) {
                $mock->shouldReceive('fails')->andReturn(false);
            }));

        $command = Mockery::mock(FlashcardInteractive::class)->makePartial();
        $command->shouldReceive('ask')->once()->andReturn('test@gmail.com');

        $result = $command->askWithValidation('Enter a value:', 'email');

        $this->assertEquals('test@gmail.com', $result);
    }

    public function testAskWithValidationFailure()
    {
        $emailRules = [
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
        $this->mock(ValidationRules::class, function ($mock) use ($emailRules) {
            $mock->shouldReceive('emailRule')->andReturn($emailRules);
        });

        Validator::shouldReceive('make')
            ->twice()
            ->andReturn(
                Mockery::mock('Illuminate\Contracts\Validation\Validator', function ($mock) {
                    $mock->shouldReceive('fails')->andReturn(true);
                    $mock->shouldReceive('errors')->andReturn(new MessageBag());
                }),
                Mockery::mock('Illuminate\Contracts\Validation\Validator', function ($mock) {
                    $mock->shouldReceive('fails')->andReturn(false);
                })
            );

        $command = Mockery::mock(FlashcardInteractive::class)->makePartial();
        $command->shouldReceive('ask')
            ->once()
            ->andReturn('invalid_email'); // First call returns invalid input
        $command->shouldReceive('ask')
            ->once()
            ->andReturn('test@gmail.com'); // Second call returns valid input
        $command->shouldReceive('error')->once()->andReturn('The email must be a valid email address.');

        $result = $command->askWithValidation('Enter a value:', 'email');

        $this->assertEquals('test@gmail.com', $result);
    }
}
