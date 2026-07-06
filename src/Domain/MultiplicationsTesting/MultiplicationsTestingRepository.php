<?php

declare(strict_types=1);

namespace App\Domain\MultiplicationsTesting;

interface MultiplicationsTestingRepository
{
    public function generateRandom(int $multiplier): MultiplicationsTesting;

    public function generateRandomFromAll(): MultiplicationsTesting;

    /**
     * @param int[] $multipliers
     */
    public function generateRandomFromSelected(array $multipliers): MultiplicationsTesting;

    /** @return int[] */
    public function getAvailableTables(): array;
}
