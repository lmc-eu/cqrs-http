<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Handler;

use Lmc\Cqrs\Http\AbstractHttpTestCase;
use Lmc\Cqrs\Http\Exception\HttpBadRequestException;
use Lmc\Cqrs\Http\Exception\HttpServerErrorException;
use Lmc\Cqrs\Http\Fixture\DummyHttpGetQuery;
use Lmc\Cqrs\Types\Exception\CqrsExceptionInterface;
use Lmc\Cqrs\Types\ValueObject\OnErrorCallback;
use Lmc\Cqrs\Types\ValueObject\OnSuccessCallback;
use Psr\Http\Message\ResponseInterface;

class HttpQueryHandlerTest extends AbstractHttpTestCase
{
    private HttpQueryHandler $httpQueryHandler;

    protected function setUp(): void
    {
        $this->httpQueryHandler = new HttpQueryHandler($this->client);
    }

    /**
     * @test
     */
    public function shouldFetchHttpQuery(): void
    {
        $uri = 'some-url';
        $data = 'fresh-data';

        $query = new DummyHttpGetQuery($uri, $this->requestFactory);

        $request = $this->prepareRequest($uri, 'GET');
        $response = $this->prepareResponse($data);

        $this->expectRequestFactoryToCreateRequestOnce($uri, $request);
        $this->expectClientToSendRequestOnce($request, $response);

        $this->httpQueryHandler->handle(
            $query,
            new OnSuccessCallback(
                fn (ResponseInterface $response) => $this->assertSame($data, $response->getBody()->getContents())
            ),
            new OnErrorCallback(fn (\Throwable $error) => $this->fail($error->getMessage())),
        );
    }

    /**
     * @test
     */
    public function shouldFetchHttpQueryAsErrorWithBadRequest(): void
    {
        $uri = 'some-url';
        $data = 'fresh-data';

        $query = new DummyHttpGetQuery($uri, $this->requestFactory);

        $request = $this->prepareRequest($uri, 'GET');
        $response = $this->prepareResponse($data, 400);

        $this->expectRequestFactoryToCreateRequestOnce($uri, $request);
        $this->expectClientToSendRequestOnce($request, $response);

        $this->httpQueryHandler->handle(
            $query,
            new OnSuccessCallback(fn (ResponseInterface $response) => $this->fail('Error was expected')),
            new OnErrorCallback(function (\Throwable $error) use ($response, $request): void {
                $this->assertInstanceOf(CqrsExceptionInterface::class, $error);
                $this->assertInstanceOf(HttpBadRequestException::class, $error);

                if ($error instanceof HttpBadRequestException) {
                    $this->assertSame($request, $error->getRequest());
                    $this->assertSame($response, $error->getResponse());
                }
            }),
        );
    }

    /**
     * @test
     */
    public function shouldFetchHttpQueryAsErrorWithServerError(): void
    {
        $uri = 'some-url';
        $data = 'fresh-data';

        $query = new DummyHttpGetQuery($uri, $this->requestFactory);

        $request = $this->prepareRequest($uri, 'GET');
        $response = $this->prepareResponse($data, 500);

        $this->expectRequestFactoryToCreateRequestOnce($uri, $request);
        $this->expectClientToSendRequestOnce($request, $response);

        $this->httpQueryHandler->handle(
            $query,
            new OnSuccessCallback(fn (ResponseInterface $response) => $this->fail('Error was expected')),
            new OnErrorCallback(function (\Throwable $error) use ($response, $request): void {
                $this->assertInstanceOf(CqrsExceptionInterface::class, $error);
                $this->assertInstanceOf(HttpServerErrorException::class, $error);

                if ($error instanceof HttpServerErrorException) {
                    $this->assertSame($request, $error->getRequest());
                    $this->assertSame($response, $error->getResponse());
                }
            }),
        );
    }
}
