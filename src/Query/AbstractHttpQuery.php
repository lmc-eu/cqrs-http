<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Query;

use Lmc\Cqrs\Types\Feature\ProfileableInterface;
use Lmc\Cqrs\Types\QueryInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * @phpstan-implements QueryInterface<RequestInterface>
 */
abstract class AbstractHttpQuery implements QueryInterface, ProfileableInterface
{
    protected RequestFactoryInterface $requestFactory;

    public function __construct(RequestFactoryInterface $requestFactory)
    {
        $this->requestFactory = $requestFactory;
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

    /** @return UriInterface|string */
    abstract public function getUri();

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
