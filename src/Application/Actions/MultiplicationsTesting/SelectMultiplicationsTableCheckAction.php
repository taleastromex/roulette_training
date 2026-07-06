<?php

declare(strict_types=1);

namespace App\Application\Actions\MultiplicationsTesting;

use App\Application\Actions\Action;
use App\Domain\MultiplicationsTesting\MultiplicationsTestingRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use Slim\Exception\HttpBadRequestException;

class SelectMultiplicationsTableCheckAction extends Action
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
        $body      = (array) $this->getFormData();
        $raw       = isset($body['t']) && is_array($body['t']) ? $body['t'] : [];
        $selected  = array_values(array_intersect(
            array_map('intval', $raw),
            $this->repository->getAvailableTables()
        ));

        if (empty($selected)) {
            throw new HttpBadRequestException($this->request, 'Select at least one table.');
        }

        return $this->response
            ->withHeader('Location', '/multiplications-table/custom?' . http_build_query(['t' => $selected]))
            ->withStatus(302);
    }
}
