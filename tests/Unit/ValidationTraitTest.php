<?php

namespace Tests\Unit;

use App\Traits\ValidationTrait;
use App\Validators\ValidationRules;
use Illuminate\Support\Facades\Validator;
use Mockery;
use Tests\TestCase;

class ValidationTraitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->command = Mockery::mock('command');
        $this->command->shouldReceive('ask')->andReturn('validValue');
        $this->command->shouldReceive('error');

        $trait = new class {
            use ValidationTrait;
            public $command;
        };
        $trait->command = $this->command;
        $this->trait = $trait;
    }

    public function testAskWithValidationReturnsValidValue()
    {
        $data = [
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
        $this->mock(ValidationRules::class, function ($mock) use ($data) {
            $mock->shouldReceive('emailRule')->andReturn($data);
        });

        Validator::shouldReceive('make')
            ->with(['email' => 'validValue'], $data['rules'], $data['messages'])
            ->andReturnSelf();

        Validator::shouldReceive('fails')->andReturn(false);

        $result = $this->trait->askWithValidation('Enter value:', 'email');

        $this->assertEquals('validValue', $result);
    }

    public function testAskWithValidationRetriesOnInvalidValue()
    {
        $data = [
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
        $this->mock(ValidationRules::class, function ($mock) use ($data) {
            $mock->shouldReceive('emailRule')->andReturn($data);
        });

        Validator::shouldReceive('make')
            ->with(['email' => 'invalidValue'], $data['rules'], $data['messages'])
            ->andReturnSelf();

        Validator::shouldReceive('make')
            ->with(['email' => 'validValue'], $data['rules'], $data['messages'])
            ->andReturnSelf();

        Validator::shouldReceive('fails')->andReturn(true, false);
        Validator::shouldReceive('errors')->andReturnSelf();
        Validator::shouldReceive('first')->andReturn('The email must be a valid email address.');

        $result = $this->trait->askWithValidation('Enter value:', 'email');

        $this->assertEquals('validValue', $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
