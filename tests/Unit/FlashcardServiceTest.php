<?php

namespace Tests\Unit;

use App\Models\Flashcard;
use App\Repositories\FlashcardRepository;
use App\Services\FlashcardService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class FlashcardServiceTest extends TestCase
{
    protected $flashcardRepository;
    protected $flashcardService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->flashcardRepository = Mockery::mock(FlashcardRepository::class);
        $this->flashcardService = new FlashcardService($this->flashcardRepository);
    }

    public function testCreateFlashcard()
    {
        $question = 'What is Laravel?';
        $answer = 'A PHP framework';

        $data = [
            'question' => trim($question),
            'answer' => trim($answer)
        ];

        $flashcard = new Flashcard($data);

        $this->flashcardRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($flashcard);

        $result = $this->flashcardService->createFlashcard($question, $answer);

        $this->assertInstanceOf(Flashcard::class, $result);
        $this->assertEquals($question, $result->question);
        $this->assertEquals($answer, $result->answer);
    }

    public function testListFlashcards()
    {
        $flashcards = new Collection([
            new Flashcard(['question' => 'Question 1', 'answer' => 'Answer 1']),
            new Flashcard(['question' => 'Question 2', 'answer' => 'Answer 2'])
        ]);

        $this->flashcardRepository
            ->shouldReceive('getFlashcards')
            ->once()
            ->andReturn($flashcards);

        $result = $this->flashcardService->listFlashcards();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
