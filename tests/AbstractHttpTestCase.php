<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

abstract class AbstractHttpTestCase extends TestCase
{
    /** @var ClientInterface|MockObject */
    protected ClientInterface $client;
    /** @var RequestFactoryInterface|MockObject */
    protected RequestFactoryInterface $requestFactory;

    /** @before */
    public function setUpClient(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
    }

    /** @before */
    public function setUpRequestFactory(): void
    {
        $this->requestFactory = $this->createMock(RequestFactoryInterface::class);
    }

    /** @return RequestInterface|MockObject */
    protected function prepareRequest(string $url, string $method): RequestInterface
    {
        $request = $this->createMock(RequestInterface::class);

        $request->expects($this->any())
            ->method('getUri')
            ->willReturn($url);

        $request->expects($this->any())
            ->method('getMethod')
            ->willReturn($method);

        $request->expects($this->any())
            ->method('withBody')
            ->willReturnSelf();

        return $request;
    }

    /** @return ResponseInterface|MockObject */
    protected function prepareResponse(string $body, int $statusCode = 200): ResponseInterface
    {
        $response = $this->createMock(ResponseInterface::class);

        $response->expects($this->any())
            ->method('getBody')
            ->willReturn($this->prepareStream($body));

        $response->expects($this->any())
            ->method('getStatusCode')
            ->willReturn($statusCode);

        return $response;
    }

    /** @return StreamInterface|MockObject */
    protected function prepareStream(string $body): StreamInterface
    {
        $stream = $this->createMock(StreamInterface::class);

        $stream->expects($this->any())
            ->method('getContents')
            ->willReturn($body);

        return $stream;
    }

    protected function expectRequestFactoryToCreateRequestOnce(string $uri, RequestInterface $request): void
    {
        $this->requestFactory->expects($this->once())
            ->method('createRequest')
            ->with($request->getMethod(), $uri)
            ->willReturn($request);
    }

    protected function expectClientToSendRequestOnce(RequestInterface $request, ResponseInterface $response): void
    {
        $this->client->expects($this->once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($response);
    }
}
