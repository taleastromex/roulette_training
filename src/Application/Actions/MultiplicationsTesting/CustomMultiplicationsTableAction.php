<?php

declare(strict_types=1);

namespace App\Application\Actions\MultiplicationsTesting;

use App\Application\Actions\Action;
use App\Domain\MultiplicationsTesting\MultiplicationsTestingRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

class CustomMultiplicationsTableAction extends Action
{
    use ParsesSelectedTables;

    public function __construct(
        LoggerInterface $logger,
        private Twig $twig,
        private MultiplicationsTestingRepository $repository
    ) {
        parent::__construct($logger);
    }

    protected function action(): Response
    {
        $selected = $this->parseSelectedTables($this->request, $this->repository);
        $question = $this->repository->generateRandomFromSelected($selected);

        return $this->twig
            ->render($this->response, 'multiplications-table.html.twig', [
                'custom'   => true,
                'selected' => $selected,
                'question' => $question,
            ])
            ->withHeader('Content-Type', 'text/html; charset=utf-8');
    }
}
