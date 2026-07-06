<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Domain\MultiplicationsTesting\MultiplicationsTestingRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

class IndexAction extends Action
{
    public function __construct(
        LoggerInterface $logger,
        private Twig $twig,
        private MultiplicationsTestingRepository $repository
    ) {
        parent::__construct($logger);
    }

    protected function action(): Response
    {
        $tables = [
            ['name' => 'Все таблицы (вперемешку)', 'slug' => 'multiplications-table'],
            ['name' => 'Выбрать таблицы',          'slug' => 'multiplications-table/select'],
        ];

        foreach ($this->repository->getAvailableTables() as $multiplier) {
            $tables[] = [
                'name' => "Таблица × {$multiplier}",
                'slug' => "multiplications-table/{$multiplier}",
            ];
        }

        return $this->twig
            ->render($this->response, 'index.html.twig', [
                'testing_list' => $tables,
            ])
            ->withHeader('Content-Type', 'text/html; charset=utf-8');
    }
}
