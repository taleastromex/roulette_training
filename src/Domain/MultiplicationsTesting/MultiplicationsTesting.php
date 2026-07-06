<?php

declare(strict_types=1);

namespace App\Domain\MultiplicationsTesting;

use JsonSerializable;

class MultiplicationsTesting implements JsonSerializable
{
    public function __construct(
        private int $firstTerm,
        private int $secondTerm
    ) {}

    public function getFirstTerm(): int
    {
        return $this->firstTerm;
    }

    public function getSecondTerm(): int
    {
        return $this->secondTerm;
    }

    public function getAnswer(): int
    {
        return $this->firstTerm * $this->secondTerm;
    }

    public function isCorrectAnswer(int $answer): bool
    {
        return $answer === $this->getAnswer();
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'firstTerm'  => $this->firstTerm,
            'secondTerm' => $this->secondTerm,
            'answer'     => $this->getAnswer(),
        ];
    }
}
