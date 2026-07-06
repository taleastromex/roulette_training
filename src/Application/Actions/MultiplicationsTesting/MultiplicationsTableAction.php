<?php

declare(strict_types=1);

namespace App\Application\Actions\MultiplicationsTesting;

use App\Application\Actions\Action;
use App\Domain\MultiplicationsTesting\MultiplicationsTestingRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig;

class MultiplicationsTableAction extends Action
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
        $multiplier = (int) $this->resolveArg('multiplier');

        if (!in_array($multiplier, $this->repository->getAvailableTables(), true)) {
            throw new HttpNotFoundException($this->request);
        }

        $question = $this->repository->generateRandom($multiplier);

        return $this->twig
            ->render($this->response, 'multiplications-table.html.twig', [
                'multiplier' => $multiplier,
                'question'   => $question,
            ])
            ->withHeader('Content-Type', 'text/html; charset=utf-8');
    }
}
