<?php

declare(strict_types=1);

namespace App\Application\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

class IndexAction extends Action
{
    public function __construct(LoggerInterface $logger, private Twig $twig)
    {
        parent::__construct($logger);
    }

    protected function action(): Response
    {
        return $this->twig
            ->render(
                $this->response,
                'index.html.twig',
                [
                    'testing_list' => [
                        [
                            'name' => 'Multiplications table testing',
                            'slug' => 'multiplications-table',
                        ],
                    ],
                ]
            )
            ->withHeader('Content-Type', 'text/html; charset=utf-8');
    }
}
