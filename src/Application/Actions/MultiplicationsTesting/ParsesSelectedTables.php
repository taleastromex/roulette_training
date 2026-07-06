<?php

declare(strict_types=1);

namespace App\Application\Actions\MultiplicationsTesting;

use App\Domain\MultiplicationsTesting\MultiplicationsTestingRepository;
use Slim\Exception\HttpBadRequestException;
use Psr\Http\Message\ServerRequestInterface as Request;

trait ParsesSelectedTables
{
    private function parseSelectedTables(Request $request, MultiplicationsTestingRepository $repository): array
    {
        $params    = $request->getQueryParams();
        $raw       = isset($params['t']) && is_array($params['t']) ? $params['t'] : [];
        $selected  = array_map('intval', $raw);
        $available = $repository->getAvailableTables();

        $valid = array_values(array_intersect($selected, $available));

        if (empty($valid)) {
            throw new HttpBadRequestException($request, 'No valid tables selected.');
        }

        return $valid;
    }

    private function buildQueryString(array $multipliers): string
    {
        return http_build_query(['t' => $multipliers]);
    }
}
