<?php

declare(strict_types=1);

use App\Application\Actions\IndexAction;
use App\Application\Actions\MultiplicationsTesting\CustomMultiplicationsTableAction;
use App\Application\Actions\MultiplicationsTesting\CustomMultiplicationsTableCheckAction;
use App\Application\Actions\MultiplicationsTesting\MixedMultiplicationsTableAction;
use App\Application\Actions\MultiplicationsTesting\MixedMultiplicationsTableCheckAction;
use App\Application\Actions\MultiplicationsTesting\MultiplicationsTableAction;
use App\Application\Actions\MultiplicationsTesting\MultiplicationsTableCheckAction;
use App\Application\Actions\MultiplicationsTesting\SelectMultiplicationsTableAction;
use App\Application\Actions\MultiplicationsTesting\SelectMultiplicationsTableCheckAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', IndexAction::class);

    $app->get('/multiplications-table', MixedMultiplicationsTableAction::class);
    $app->post('/multiplications-table', MixedMultiplicationsTableCheckAction::class);

    $app->get('/multiplications-table/select', SelectMultiplicationsTableAction::class);
    $app->post('/multiplications-table/select', SelectMultiplicationsTableCheckAction::class);

    $app->get('/multiplications-table/custom', CustomMultiplicationsTableAction::class);
    $app->post('/multiplications-table/custom', CustomMultiplicationsTableCheckAction::class);

    $app->get('/multiplications-table/{multiplier:[0-9]+}', MultiplicationsTableAction::class);
    $app->post('/multiplications-table/{multiplier:[0-9]+}', MultiplicationsTableCheckAction::class);

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
