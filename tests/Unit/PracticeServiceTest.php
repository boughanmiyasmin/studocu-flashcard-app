<?php

namespace Tests\Unit;

use App\Constants;
use App\Models\Flashcard;
use App\Models\Practice;
use App\Services\PracticeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class PracticeServiceTest extends TestCase
{
    use RefreshDatabase;
    protected PracticeService $practiceService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->practiceService = $this->app->make(PracticeService::class);
    }

    public function testResetProgress()
    {
        $email = 'test@example.com';

        $flashcard = Flashcard::factory()->create();
        $practice = Practice::factory()->create([
            'flashcard_id' => $flashcard->id,
            'email' => $email,
            'status' => Constants::CORRECT,
        ]);
        $practice1 = Practice::factory()->create([
            'flashcard_id' => $flashcard->id,
            'email' => 'other@gmail.com'
        ]);

        $this->practiceService->resetProgress($email);

        $practice->refresh();

        $this->assertEquals(Constants::NOT_ANSWERED ,$practice->status);
        $this->assertNotEquals(Constants::NOT_ANSWERED ,$practice1->status);
    }

    public function testGetCurrentFlashcardsPractice()
    {
        $email = 'test@example.com';
        $flashcard1 = Flashcard::factory()->create();
        $flashcard2 = Flashcard::factory()->create();
        Practice::factory()->create([
            'flashcard_id' => $flashcard1->id,
            'email' => $email,
            'status' => Constants::CORRECT,
        ]);
        Practice::factory()->create([
            'flashcard_id' => $flashcard1->id,
            'email' => 'other@gmail.com'
        ]);
        Practice::factory()->create([
            'flashcard_id' => $flashcard2->id,
            'email' => $email
        ]);

        $result = $this->practiceService->practiceFlashcards($email);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }

    public function testGetUserFlashcardPractice()
    {
        $email = 'test@example.com';

        $flashcard = Flashcard::factory()->create();
        $practice = Practice::factory()->create([
            'flashcard_id' => $flashcard->id,
            'email' => $email,
            'status' => Constants::INCORRECT,
        ]);
        Practice::factory()->create([
            'flashcard_id' => $flashcard->id,
            'email' => 'other@gmail.com'
        ]);

        $flashcardPractice = $this->practiceService->practiceFlashcard($flashcard->id, $email);

        $this->assertEquals($practice->id, $flashcardPractice->getPractice()->id);

        $this->assertInstanceOf(Flashcard::class, $flashcardPractice);
    }

    public function testGetUserFlashcardPracticeThrowsException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(Constants::INVALID_OPTION_MESSAGE);

        $email = 'test@example.com';

        $flashcard = Flashcard::factory()->create();
        Practice::factory()->create([
            'flashcard_id' => $flashcard->id,
            'email' => $email,
            'status' => Constants::CORRECT,
        ]);
        Practice::factory()->create([
            'flashcard_id' => $flashcard->id,
            'email' => 'other@gmail.com'
        ]);

        $this->practiceService->practiceFlashcard(52, $email);
    }

    public function testCompletionPercentage()
    {
        $email = 'test@example.com';

        $flashcard = Flashcard::factory()->create();
        $flashcard2 = Flashcard::factory()->create();
        Practice::factory()->create([
            'flashcard_id' => $flashcard->id,
            'email' => $email,
            'status' => Constants::CORRECT,
        ]);
        Practice::factory()->create([
            'flashcard_id' => $flashcard2->id,
            'email' => 'other@gmail.com',
            'status' => Constants::CORRECT,
        ]);

        Practice::factory()->create([
            'flashcard_id' => $flashcard2->id,
            'email' => $email,
            'status' => Constants::NOT_ANSWERED,
        ]);

        $flashcards =  $this->practiceService->practiceFlashcards($email);
        $result = $this->practiceService->completionPercentage($flashcards);

        $this->assertEquals(50, $result);
    }

    public function testCorrectAnswersPercentage()
    {
        $email = 'test@example.com';

        $flashcard = Flashcard::factory()->create();
        $flashcard2 = Flashcard::factory()->create();
        Practice::factory()->create([
            'flashcard_id' => $flashcard->id,
            'email' => $email,
            'status' => Constants::CORRECT,
        ]);
        Practice::factory()->create([
            'flashcard_id' => $flashcard2->id,
            'email' => 'other@gmail.com',
            'status' => Constants::CORRECT,
        ]);

        Practice::factory()->create([
            'flashcard_id' => $flashcard2->id,
            'email' => $email,
            'status' => Constants::CORRECT,
        ]);

        $flashcards =  $this->practiceService->practiceFlashcards($email);
        $result = $this->practiceService->correctAnswersPercentage($flashcards);

        $this->assertEquals(100, $result);
    }

    public function testProcessCorrectAnswer()
    {
        $email = 'test@example.com';
        $answer = 'daisy';
        $flashcard = Flashcard::factory()->create([
            'answer' => $answer
        ]);
        $flashcard2 = Flashcard::factory()->create();
        Practice::factory()->create([
            'flashcard_id' => $flashcard->id,
            'email' => $email,
            'status' => Constants::CORRECT,
        ]);
        Practice::factory()->create([
            'flashcard_id' => $flashcard2->id,
            'email' => 'other@gmail.com',
            'status' => Constants::CORRECT,
        ]);

        Practice::factory()->create([
            'flashcard_id' => $flashcard2->id,
            'email' => $email,
            'status' => Constants::CORRECT,
        ]);

        $flashcard = $this->practiceService->practiceFlashcard($flashcard->id, $email);
        $status = $this->practiceService->processAnswer($flashcard, $email, $answer);

        $this->assertEquals(Constants::CORRECT, $status);
    }

    public function testProcessIncorrectAnswer()
    {
        $email = 'test@example.com';
        $answer = 'Jasmin';
        $flashcard = Flashcard::factory()->create([
            'answer' => 'daisy'
        ]);
        $flashcard2 = Flashcard::factory()->create();
        Practice::factory()->create([
            'flashcard_id' => $flashcard->id,
            'email' => $email,
            'status' => Constants::CORRECT,
        ]);
        Practice::factory()->create([
            'flashcard_id' => $flashcard2->id,
            'email' => 'other@gmail.com',
            'status' => Constants::CORRECT,
        ]);

        Practice::factory()->create([
            'flashcard_id' => $flashcard2->id,
            'email' => $email,
            'status' => Constants::CORRECT,
        ]);

        $flashcard = $this->practiceService->practiceFlashcard($flashcard->id, $email);
        $status = $this->practiceService->processAnswer($flashcard, $email, $answer);

        $this->assertEquals(Constants::INCORRECT, $status);
    }
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
