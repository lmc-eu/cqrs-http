<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Command;

use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

abstract class AbstractHttpPostCommand extends AbstractHttpCommand
{
    final public function getHttpMethod(): string
    {
        return RequestMethodInterface::METHOD_POST;
    }

    public function createRequest(): RequestInterface
    {
        return ($request = parent::createRequest()->withBody($this->createBody())) instanceof RequestInterface
            ? $request
            : throw new \LogicException('Implementation of the RequestInterface->withBody(...) does not return a RequestInterface.');
    }

    abstract public function createBody(): StreamInterface;

    public function getProfilerData(): ?array
    {
        return [
            'Body' => $this->createBody(),
        ];
    }
}
