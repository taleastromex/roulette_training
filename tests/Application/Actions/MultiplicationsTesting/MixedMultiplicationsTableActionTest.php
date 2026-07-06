<?php

declare(strict_types=1);

namespace Tests\Application\Actions\MultiplicationsTesting;

use App\Application\Handlers\HttpErrorHandler;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class MixedMultiplicationsTableActionTest extends TestCase
{
    public function testGetReturns200(): void
    {
        $app      = $this->getAppInstance();
        $request  = $this->createRequest('GET', '/multiplications-table');
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetReturnsHtmlContentType(): void
    {
        $app      = $this->getAppInstance();
        $request  = $this->createRequest('GET', '/multiplications-table');
        $response = $app->handle($request);

        $this->assertStringContainsString('text/html', $response->getHeaderLine('Content-Type'));
    }

    public function testGetRendersAllTablesBadge(): void
    {
        $app      = $this->getAppInstance();
        $request  = $this->createRequest('GET', '/multiplications-table');
        $response = $app->handle($request);
        $body     = (string) $response->getBody();

        $this->assertStringContainsString('Все таблицы', $body);
    }

    public function testGetRendersQuestionFromAvailableTable(): void
    {
        $app      = $this->getAppInstance();
        $request  = $this->createRequest('GET', '/multiplications-table');
        $response = $app->handle($request);
        $body     = (string) $response->getBody();

        $this->assertStringContainsString('×', $body);
        $this->assertStringContainsString('name="first_term"', $body);
        $this->assertStringContainsString('name="second_term"', $body);
    }

    public function testGetFormPostsToMixedRoute(): void
    {
        $app      = $this->getAppInstance();
        $request  = $this->createRequest('GET', '/multiplications-table');
        $response = $app->handle($request);
        $body     = (string) $response->getBody();

        $this->assertStringContainsString('action="/multiplications-table"', $body);
    }

    public function testPostWithCorrectAnswerShowsSuccess(): void
    {
        $app     = $this->getAppInstance();
        $request = $this->createRequest('POST', '/multiplications-table', [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);
        $request = $request->withParsedBody([
            'first_term'  => '5',
            'second_term' => '4',
            'answer'      => '20',
        ]);

        $response = $app->handle($request);
        $body     = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Верно', $body);
    }

    public function testPostWithWrongAnswerShowsFailureWithExpected(): void
    {
        $app     = $this->getAppInstance();
        $request = $this->createRequest('POST', '/multiplications-table', [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);
        $request = $request->withParsedBody([
            'first_term'  => '11',
            'second_term' => '3',
            'answer'      => '99',
        ]);

        $response = $app->handle($request);
        $body     = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Неверно', $body);
        $this->assertStringContainsString('33', $body); // 11 × 3 = 33
    }

    public function testPostRendersNextQuestionInMixedMode(): void
    {
        $app     = $this->getAppInstance();
        $request = $this->createRequest('POST', '/multiplications-table', [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);
        $request = $request->withParsedBody([
            'first_term'  => '17',
            'second_term' => '2',
            'answer'      => '34',
        ]);

        $response = $app->handle($request);
        $body     = (string) $response->getBody();

        $this->assertStringContainsString('Все таблицы', $body);
        $this->assertStringContainsString('name="first_term"', $body);
        $this->assertStringContainsString('name="second_term"', $body);
    }
}
