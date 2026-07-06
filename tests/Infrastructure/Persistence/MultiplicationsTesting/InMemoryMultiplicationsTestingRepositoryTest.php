<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\MultiplicationsTesting;

use App\Domain\MultiplicationsTesting\MultiplicationsTesting;
use App\Infrastructure\Persistence\MultiplicationsTesting\InMemoryMultiplicationsTestingRepository;
use PHPUnit\Framework\TestCase;

class InMemoryMultiplicationsTestingRepositoryTest extends TestCase
{
    private InMemoryMultiplicationsTestingRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new InMemoryMultiplicationsTestingRepository();
    }

    public function testGetAvailableTablesReturnsExpected(): void
    {
        $this->assertEquals([5, 8, 11, 17, 35], $this->repository->getAvailableTables());
    }

    public function testGenerateRandomReturnsMultiplicationsTesting(): void
    {
        $question = $this->repository->generateRandom(5);

        $this->assertInstanceOf(MultiplicationsTesting::class, $question);
    }

    public function testGenerateRandomKeepsMultiplierFixed(): void
    {
        foreach ([5, 8, 11, 17, 35] as $multiplier) {
            $question = $this->repository->generateRandom($multiplier);

            $this->assertEquals($multiplier, $question->getFirstTerm());
        }
    }

    public function testGenerateRandomSecondTermIsInRange(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $question = $this->repository->generateRandom(5);

            $this->assertGreaterThanOrEqual(1, $question->getSecondTerm());
            $this->assertLessThanOrEqual(20, $question->getSecondTerm());
        }
    }

    public function testGenerateRandomFromAllReturnsValidMultiplier(): void
    {
        $availableTables = $this->repository->getAvailableTables();

        for ($i = 0; $i < 30; $i++) {
            $question = $this->repository->generateRandomFromAll();

            $this->assertContains($question->getFirstTerm(), $availableTables);
            $this->assertGreaterThanOrEqual(1, $question->getSecondTerm());
            $this->assertLessThanOrEqual(20, $question->getSecondTerm());
        }
    }

    public function testGenerateRandomFromAllProducesVariedMultipliers(): void
    {
        $multipliers = [];
        for ($i = 0; $i < 50; $i++) {
            $multipliers[] = $this->repository->generateRandomFromAll()->getFirstTerm();
        }

        $this->assertGreaterThan(1, count(array_unique($multipliers)));
    }

    public function testGenerateRandomFromSelectedReturnsOnlySelectedMultipliers(): void
    {
        $selected = [5, 17];

        for ($i = 0; $i < 30; $i++) {
            $question = $this->repository->generateRandomFromSelected($selected);

            $this->assertContains($question->getFirstTerm(), $selected);
        }
    }

    public function testGenerateRandomFromSelectedProducesVariedMultipliersWhenMultipleSelected(): void
    {
        $selected    = [5, 8, 11, 17, 35];
        $multipliers = [];

        for ($i = 0; $i < 50; $i++) {
            $multipliers[] = $this->repository->generateRandomFromSelected($selected)->getFirstTerm();
        }

        $this->assertGreaterThan(1, count(array_unique($multipliers)));
    }

    public function testGenerateRandomFromSelectedWithSingleTable(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $question = $this->repository->generateRandomFromSelected([35]);

            $this->assertEquals(35, $question->getFirstTerm());
        }
    }

    public function testGenerateRandomProducesVariedSecondTerms(): void
    {
        $terms = [];
        for ($i = 0; $i < 30; $i++) {
            $terms[] = $this->repository->generateRandom(5)->getSecondTerm();
        }

        $this->assertGreaterThan(1, count(array_unique($terms)));
    }
}
