<?php

declare(strict_types=1);

namespace Tests\Application\Actions\MultiplicationsTesting;

use App\Application\Handlers\HttpErrorHandler;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class CustomMultiplicationsTableActionTest extends TestCase
{
    private function getAppWithErrorHandler(): \Slim\App
    {
        $app              = $this->getAppInstance();
        $callableResolver = $app->getCallableResolver();
        $responseFactory  = $app->getResponseFactory();
        $errorHandler     = new HttpErrorHandler($callableResolver, $responseFactory);
        $errorMiddleware  = new ErrorMiddleware($callableResolver, $responseFactory, true, false, false);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);
        $app->add($errorMiddleware);

        return $app;
    }

    public function testGetReturns200WithValidTables(): void
    {
        $app     = $this->getAppInstance();
        $request = $this->createRequest('GET', '/multiplications-table/custom')
            ->withQueryParams(['t' => ['5', '11']]);

        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetRendersSelectedBadges(): void
    {
        $app     = $this->getAppInstance();
        $request = $this->createRequest('GET', '/multiplications-table/custom')
            ->withQueryParams(['t' => ['5', '17']]);

        $body = (string) $app->handle($request)->getBody();

        $this->assertStringContainsString('× 5', $body);
        $this->assertStringContainsString('× 17', $body);
    }

    public function testGetFormPostsToCustomRouteWithParams(): void
    {
        $app     = $this->getAppInstance();
        $request = $this->createRequest('GET', '/multiplications-table/custom')
            ->withQueryParams(['t' => ['5', '8']]);

        $body = (string) $app->handle($request)->getBody();

        $this->assertStringContainsString('/multiplications-table/custom', $body);
    }

    public function testGetRendersQuestionFromSelectedTable(): void
    {
        $app     = $this->getAppInstance();
        $request = $this->createRequest('GET', '/multiplications-table/custom')
            ->withQueryParams(['t' => ['35']]);

        $body = (string) $app->handle($request)->getBody();

        $this->assertStringContainsString('35', $body);
        $this->assertStringContainsString('×', $body);
    }

    public function testGetReturnsBadRequestWithNoTables(): void
    {
        $app      = $this->getAppWithErrorHandler();
        $request  = $this->createRequest('GET', '/multiplications-table/custom');
        $response = $app->handle($request);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testGetReturnsBadRequestWithInvalidTables(): void
    {
        $app     = $this->getAppWithErrorHandler();
        $request = $this->createRequest('GET', '/multiplications-table/custom')
            ->withQueryParams(['t' => ['999']]);

        $response = $app->handle($request);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testGetBackLinkGoesToSelectPage(): void
    {
        $app     = $this->getAppInstance();
        $request = $this->createRequest('GET', '/multiplications-table/custom')
            ->withQueryParams(['t' => ['5']]);

        $body = (string) $app->handle($request)->getBody();

        $this->assertStringContainsString('/multiplications-table/select', $body);
    }

    public function testPostWithCorrectAnswerShowsSuccess(): void
    {
        $app     = $this->getAppInstance();
        $request = $this->createRequest('POST', '/multiplications-table/custom', [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])
            ->withQueryParams(['t' => ['5', '8']])
            ->withParsedBody([
                'first_term'  => '5',
                'second_term' => '6',
                'answer'      => '30',
            ]);

        $body = (string) $app->handle($request)->getBody();

        $this->assertStringContainsString('Верно', $body);
    }

    public function testPostWithWrongAnswerShowsFailure(): void
    {
        $app     = $this->getAppInstance();
        $request = $this->createRequest('POST', '/multiplications-table/custom', [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])
            ->withQueryParams(['t' => ['11', '17']])
            ->withParsedBody([
                'first_term'  => '17',
                'second_term' => '4',
                'answer'      => '99',
            ]);

        $response = $app->handle($request);
        $body     = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Неверно', $body);
        $this->assertStringContainsString('68', $body); // 17 × 4 = 68
    }

    public function testPostRendersNextQuestionFromSelectedTables(): void
    {
        $app     = $this->getAppInstance();
        $request = $this->createRequest('POST', '/multiplications-table/custom', [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])
            ->withQueryParams(['t' => ['5', '35']])
            ->withParsedBody([
                'first_term'  => '5',
                'second_term' => '3',
                'answer'      => '15',
            ]);

        $body = (string) $app->handle($request)->getBody();

        $this->assertStringContainsString('name="first_term"', $body);
        $this->assertStringContainsString('name="second_term"', $body);
    }
}
