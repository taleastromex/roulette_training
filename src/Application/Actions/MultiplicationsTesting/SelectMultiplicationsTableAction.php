<?php

declare(strict_types=1);

namespace App\Application\Actions\MultiplicationsTesting;

use App\Application\Actions\Action;
use App\Domain\MultiplicationsTesting\MultiplicationsTestingRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

class SelectMultiplicationsTableAction extends Action
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
        return $this->twig
            ->render($this->response, 'select-tables.html.twig', [
                'available' => $this->repository->getAvailableTables(),
            ])
            ->withHeader('Content-Type', 'text/html; charset=utf-8');
    }
}
