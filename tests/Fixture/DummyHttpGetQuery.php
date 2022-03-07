<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Fixture;

use Lmc\Cqrs\Http\Query\AbstractHttpGetQuery;
use Psr\Http\Message\RequestFactoryInterface;

class DummyHttpGetQuery extends AbstractHttpGetQuery
{
    public function __construct(private string $uri, RequestFactoryInterface $requestFactory)
    {
        parent::__construct($requestFactory);
    }

    public function getUri(): string
    {
        return $this->uri;
    }
}
