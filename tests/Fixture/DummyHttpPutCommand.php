<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Fixture;

use Lmc\Cqrs\Http\Command\AbstractHttpPutCommand;
use Psr\Http\Message\RequestFactoryInterface;

class DummyHttpPutCommand extends AbstractHttpPutCommand
{
    private string $uri;

    public function __construct(string $uri, RequestFactoryInterface $requestFactory)
    {
        $this->uri = $uri;
        parent::__construct($requestFactory);
    }

    public function getUri(): string
    {
        return $this->uri;
    }
}
