<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Fixture;

use Lmc\Cqrs\Http\Command\AbstractHttpPostCommand;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamInterface;

class DummyHttpPostCommand extends AbstractHttpPostCommand
{
    public function __construct(private string $uri, private StreamInterface $body, RequestFactoryInterface $requestFactory)
    {
        parent::__construct($requestFactory);
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function createBody(): StreamInterface
    {
        return $this->body;
    }
}
