<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Handler;

use Lmc\Cqrs\Http\AbstractHttpTestCase;
use Lmc\Cqrs\Http\Exception\HttpBadRequestException;
use Lmc\Cqrs\Http\Exception\HttpServerErrorException;
use Lmc\Cqrs\Http\Fixture\DummyHttpPostCommand;
use Lmc\Cqrs\Http\Fixture\DummyHttpPutCommand;
use Lmc\Cqrs\Types\Exception\CqrsExceptionInterface;
use Lmc\Cqrs\Types\ValueObject\OnErrorCallback;
use Lmc\Cqrs\Types\ValueObject\OnSuccessCallback;
use Psr\Http\Message\ResponseInterface;

class HttpSendCommandHandlerTest extends AbstractHttpTestCase
{
    private HttpSendCommandHandler $httpSendCommandHandler;

    protected function setUp(): void
    {
        $this->httpSendCommandHandler = new HttpSendCommandHandler($this->client);
    }

    /**
     * @test
     */
    public function shouldSendPostCommand(): void
    {
        $uri = 'some-url';
        $body = 'command-data';
        $responseData = 'response-data';

        $bodyStream = $this->prepareStream($body);

        $command = new DummyHttpPostCommand($uri, $bodyStream, $this->requestFactory);

        $request = $this->prepareRequest($uri, 'POST');
        $response = $this->prepareResponse($responseData);

        $this->expectRequestFactoryToCreateRequestOnce($uri, $request);
        $this->expectClientToSendRequestOnce($request, $response);

        $this->httpSendCommandHandler->handle(
            $command,
            new OnSuccessCallback(
                fn (ResponseInterface $response) => $this->assertSame($responseData, $response->getBody()->getContents())
            ),
            new OnErrorCallback(fn (\Throwable $error) => $this->fail($error->getMessage())),
        );
    }

    /**
     * @test
     */
    public function shouldSendPutCommand(): void
    {
        $uri = 'some-url';
        $responseData = 'response-data';

        $command = new DummyHttpPutCommand($uri, $this->requestFactory);

        $request = $this->prepareRequest($uri, 'PUT');
        $response = $this->prepareResponse($responseData);

        $this->expectRequestFactoryToCreateRequestOnce($uri, $request);
        $this->expectClientToSendRequestOnce($request, $response);

        $this->httpSendCommandHandler->handle(
            $command,
            new OnSuccessCallback(
                fn (ResponseInterface $response) => $this->assertSame($responseData, $response->getBody()->getContents())
            ),
            new OnErrorCallback(fn (\Throwable $error) => $this->fail($error->getMessage())),
        );
    }

    /**
     * @test
     */
    public function shouldSendPutCommandAsErrorWithBadRequest(): void
    {
        $uri = 'some-url';
        $responseData = 'response-data';

        $command = new DummyHttpPutCommand($uri, $this->requestFactory);

        $request = $this->prepareRequest($uri, 'PUT');
        $response = $this->prepareResponse($responseData, 400);

        $this->expectRequestFactoryToCreateRequestOnce($uri, $request);
        $this->expectClientToSendRequestOnce($request, $response);

        $this->httpSendCommandHandler->handle(
            $command,
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
    public function shouldSendPutCommandAsErrorWithServerError(): void
    {
        $uri = 'some-url';
        $responseData = 'response-data';

        $command = new DummyHttpPutCommand($uri, $this->requestFactory);

        $request = $this->prepareRequest($uri, 'PUT');
        $response = $this->prepareResponse($responseData, 500);

        $this->expectRequestFactoryToCreateRequestOnce($uri, $request);
        $this->expectClientToSendRequestOnce($request, $response);

        $this->httpSendCommandHandler->handle(
            $command,
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
