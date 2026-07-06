<?php

declare(strict_types=1);

namespace Tests\Application\Actions\MultiplicationsTesting;

use App\Application\Handlers\HttpErrorHandler;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class SelectMultiplicationsTableActionTest extends TestCase
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

    public function testGetReturns200(): void
    {
        $app      = $this->getAppInstance();
        $request  = $this->createRequest('GET', '/multiplications-table/select');
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetRendersAllAvailableTables(): void
    {
        $app      = $this->getAppInstance();
        $request  = $this->createRequest('GET', '/multiplications-table/select');
        $response = $app->handle($request);
        $body     = (string) $response->getBody();

        foreach ([5, 8, 11, 17, 35] as $multiplier) {
            $this->assertStringContainsString((string) $multiplier, $body);
        }
    }

    public function testGetRendersCheckboxes(): void
    {
        $app      = $this->getAppInstance();
        $request  = $this->createRequest('GET', '/multiplications-table/select');
        $response = $app->handle($request);
        $body     = (string) $response->getBody();

        $this->assertStringContainsString('type="checkbox"', $body);
        $this->assertStringContainsString('name="t[]"', $body);
    }

    public function testPostWithValidSelectionRedirects(): void
    {
        $app     = $this->getAppInstance();
        $request = $this->createRequest('POST', '/multiplications-table/select', [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);
        $request = $request->withParsedBody(['t' => ['5', '11', '35']]);

        $response = $app->handle($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/multiplications-table/custom', $response->getHeaderLine('Location'));
        $this->assertStringContainsString('t%5B0%5D=5', $response->getHeaderLine('Location'));
        $this->assertStringContainsString('11', $response->getHeaderLine('Location'));
        $this->assertStringContainsString('35', $response->getHeaderLine('Location'));
    }

    public function testPostWithNoSelectionReturnsBadRequest(): void
    {
        $app     = $this->getAppWithErrorHandler();
        $request = $this->createRequest('POST', '/multiplications-table/select', [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);
        $request = $request->withParsedBody([]);

        $response = $app->handle($request);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testPostWithInvalidTablesReturnsBadRequest(): void
    {
        $app     = $this->getAppWithErrorHandler();
        $request = $this->createRequest('POST', '/multiplications-table/select', [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);
        $request = $request->withParsedBody(['t' => ['999', '0']]);

        $response = $app->handle($request);

        $this->assertEquals(400, $response->getStatusCode());
    }
}
