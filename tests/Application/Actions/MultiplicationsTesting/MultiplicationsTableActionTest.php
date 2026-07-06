<?php

declare(strict_types=1);

namespace Tests\Application\Actions\MultiplicationsTesting;

use App\Application\Handlers\HttpErrorHandler;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class MultiplicationsTableActionTest extends TestCase
{
    private function getAppWithErrorHandler(): \Slim\App
    {
        $app              = $this->getAppInstance();
        $callableResolver = $app->getCallableResolver();
        $responseFactory  = $app->getResponseFactory();

        $errorHandler    = new HttpErrorHandler($callableResolver, $responseFactory);
        $errorMiddleware = new ErrorMiddleware($callableResolver, $responseFactory, true, false, false);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);
        $app->add($errorMiddleware);

        return $app;
    }

    public function testGetReturns200ForValidTable(): void
    {
        $app      = $this->getAppInstance();
        $request  = $this->createRequest('GET', '/multiplications-table/5');
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetReturnsHtmlContentType(): void
    {
        $app      = $this->getAppInstance();
        $request  = $this->createRequest('GET', '/multiplications-table/8');
        $response = $app->handle($request);

        $this->assertStringContainsString('text/html', $response->getHeaderLine('Content-Type'));
    }

    public function testGetRendersMultiplierInQuestion(): void
    {
        $app      = $this->getAppInstance();
        $request  = $this->createRequest('GET', '/multiplications-table/11');
        $response = $app->handle($request);
        $body     = (string) $response->getBody();

        $this->assertStringContainsString('11', $body);
        $this->assertStringContainsString('×', $body);
    }

    public function testGetRendersFormWithSecondTermField(): void
    {
        $app      = $this->getAppInstance();
        $request  = $this->createRequest('GET', '/multiplications-table/17');
        $response = $app->handle($request);
        $body     = (string) $response->getBody();

        $this->assertStringContainsString('name="second_term"', $body);
        $this->assertStringContainsString('name="answer"', $body);
    }

    public function testGetReturns404ForUnknownTable(): void
    {
        $app      = $this->getAppWithErrorHandler();
        $request  = $this->createRequest('GET', '/multiplications-table/999');
        $response = $app->handle($request);

        $this->assertEquals(404, $response->getStatusCode());
    }

    /** @dataProvider validTableProvider */
    public function testGetReturns200ForAllValidTables(int $multiplier): void
    {
        $app      = $this->getAppInstance();
        $request  = $this->createRequest('GET', "/multiplications-table/{$multiplier}");
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public static function validTableProvider(): array
    {
        return [[5], [8], [11], [17], [35]];
    }

    public function testPostWithCorrectAnswerShowsSuccess(): void
    {
        $app     = $this->getAppInstance();
        $request = $this->createRequest('POST', '/multiplications-table/5', [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);
        $request = $request->withParsedBody([
            'second_term' => '7',
            'answer'      => '35',
        ]);

        $response = $app->handle($request);
        $body     = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Верно', $body);
    }

    public function testPostWithWrongAnswerShowsFailureWithExpected(): void
    {
        $app     = $this->getAppInstance();
        $request = $this->createRequest('POST', '/multiplications-table/8', [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);
        $request = $request->withParsedBody([
            'second_term' => '6',
            'answer'      => '99',
        ]);

        $response = $app->handle($request);
        $body     = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Неверно', $body);
        $this->assertStringContainsString('48', $body); // 8 × 6 = 48
    }

    public function testPostRendersNextQuestion(): void
    {
        $app     = $this->getAppInstance();
        $request = $this->createRequest('POST', '/multiplications-table/35', [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);
        $request = $request->withParsedBody([
            'second_term' => '3',
            'answer'      => '105',
        ]);

        $response = $app->handle($request);
        $body     = (string) $response->getBody();

        $this->assertStringContainsString('35', $body);
        $this->assertStringContainsString('name="second_term"', $body);
    }

    public function testPostReturns404ForUnknownTable(): void
    {
        $app     = $this->getAppWithErrorHandler();
        $request = $this->createRequest('POST', '/multiplications-table/999', [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);
        $request = $request->withParsedBody(['second_term' => '1', 'answer' => '1']);

        $response = $app->handle($request);

        $this->assertEquals(404, $response->getStatusCode());
    }
}
