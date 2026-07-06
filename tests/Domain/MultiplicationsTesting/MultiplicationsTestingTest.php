<?php

declare(strict_types=1);

namespace Tests\Domain\MultiplicationsTesting;

use App\Domain\MultiplicationsTesting\MultiplicationsTesting;
use PHPUnit\Framework\TestCase;

class MultiplicationsTestingTest extends TestCase
{
    public function testGetAnswer(): void
    {
        $question = new MultiplicationsTesting(3, 7);

        $this->assertEquals(21, $question->getAnswer());
    }

    public function testGetAnswerWithZero(): void
    {
        $question = new MultiplicationsTesting(0, 5);

        $this->assertEquals(0, $question->getAnswer());
    }

    public function testIsCorrectAnswerReturnsTrue(): void
    {
        $question = new MultiplicationsTesting(6, 8);

        $this->assertTrue($question->isCorrectAnswer(48));
    }

    public function testIsCorrectAnswerReturnsFalse(): void
    {
        $question = new MultiplicationsTesting(6, 8);

        $this->assertFalse($question->isCorrectAnswer(40));
    }

    public function testGetFirstTerm(): void
    {
        $question = new MultiplicationsTesting(4, 9);

        $this->assertEquals(4, $question->getFirstTerm());
    }

    public function testGetSecondTerm(): void
    {
        $question = new MultiplicationsTesting(4, 9);

        $this->assertEquals(9, $question->getSecondTerm());
    }

    public function testJsonSerialize(): void
    {
        $question = new MultiplicationsTesting(3, 4);

        $this->assertEquals(
            ['firstTerm' => 3, 'secondTerm' => 4, 'answer' => 12],
            $question->jsonSerialize()
        );
    }
}
