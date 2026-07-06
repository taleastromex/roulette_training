<?php

declare(strict_types=1);

namespace App\Application\Actions\MultiplicationsTesting;

use App\Application\Actions\Action;
use App\Domain\MultiplicationsTesting\MultiplicationsTesting;
use App\Domain\MultiplicationsTesting\MultiplicationsTestingRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

class CustomMultiplicationsTableCheckAction extends Action
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
        $selected   = $this->parseSelectedTables($this->request, $this->repository);
        $body       = (array) $this->getFormData();
        $firstTerm  = (int) ($body['first_term']  ?? 0);
        $secondTerm = (int) ($body['second_term'] ?? 0);
        $userAnswer = (int) ($body['answer']       ?? 0);

        $question  = new MultiplicationsTesting($firstTerm, $secondTerm);
        $isCorrect = $question->isCorrectAnswer($userAnswer);
        $next      = $this->repository->generateRandomFromSelected($selected);

        return $this->twig
            ->render($this->response, 'multiplications-table.html.twig', [
                'custom'   => true,
                'selected' => $selected,
                'question' => $next,
                'result'   => [
                    'multiplier' => $firstTerm,
                    'secondTerm' => $secondTerm,
                    'userAnswer' => $userAnswer,
                    'correct'    => $isCorrect,
                    'expected'   => $question->getAnswer(),
                ],
            ])
            ->withHeader('Content-Type', 'text/html; charset=utf-8');
    }
}
