<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\MultiplicationsTesting;

use App\Domain\MultiplicationsTesting\MultiplicationsTesting;
use App\Domain\MultiplicationsTesting\MultiplicationsTestingRepository;

class InMemoryMultiplicationsTestingRepository implements MultiplicationsTestingRepository
{
    private const AVAILABLE_TABLES = [5, 8, 11, 17, 35];
    private const MIN_TERM = 1;
    private const MAX_TERM = 20;

    public function generateRandom(int $multiplier): MultiplicationsTesting
    {
        return new MultiplicationsTesting(
            $multiplier,
            random_int(self::MIN_TERM, self::MAX_TERM),
        );
    }

    public function generateRandomFromAll(): MultiplicationsTesting
    {
        $multiplier = self::AVAILABLE_TABLES[array_rand(self::AVAILABLE_TABLES)];

        return $this->generateRandom($multiplier);
    }

    public function generateRandomFromSelected(array $multipliers): MultiplicationsTesting
    {
        $multiplier = $multipliers[array_rand($multipliers)];

        return $this->generateRandom($multiplier);
    }

    public function getAvailableTables(): array
    {
        return self::AVAILABLE_TABLES;
    }
}
