<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Command;

use Fig\Http\Message\RequestMethodInterface;

abstract class AbstractHttpDeleteCommand extends AbstractHttpCommand
{
    final public function getHttpMethod(): string
    {
        return RequestMethodInterface::METHOD_DELETE;
    }
}
