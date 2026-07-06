<?php

declare(strict_types=1);

use App\Domain\MultiplicationsTesting\MultiplicationsTestingRepository;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\MultiplicationsTesting\InMemoryMultiplicationsTestingRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        UserRepository::class                  => \DI\autowire(InMemoryUserRepository::class),
        MultiplicationsTestingRepository::class => \DI\autowire(InMemoryMultiplicationsTestingRepository::class),
    ]);
};
