<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Command;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Cqrs\Types\CommandInterface;
use Lmc\Cqrs\Types\Feature\ProfileableInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * @phpstan-implements CommandInterface<RequestInterface>
 */
abstract class AbstractHttpCommand implements CommandInterface, ProfileableInterface
{
    public function __construct(private RequestFactoryInterface $requestFactory)
    {
    }

    final public function getRequestType(): string
    {
        return RequestInterface::class;
    }

    public function createRequest(): RequestInterface
    {
        return $this->modifyRequest($this->requestFactory->createRequest($this->getHttpMethod(), $this->getUri()));
    }

    public function modifyRequest(RequestInterface $request): RequestInterface
    {
        return $request;
    }

    /** @see RequestMethodInterface */
    abstract public function getHttpMethod(): string;

    abstract public function getUri(): UriInterface|string;

    public function getProfilerId(): string
    {
        return sprintf('%s:%s', $this->getHttpMethod(), $this->getUri());
    }

    public function getProfilerData(): ?array
    {
        return null;
    }

    public function __toString(): string
    {
        return (string) $this->getUri();
    }
}
