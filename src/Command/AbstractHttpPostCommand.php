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
        return parent::createRequest()->withBody($this->createBody());
    }

    abstract public function createBody(): StreamInterface;

    public function getProfilerData(): ?array
    {
        return [
            'Body' => $this->createBody(),
        ];
    }
}
