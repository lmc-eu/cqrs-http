<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Query;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Cqrs\Types\Feature\CacheableInterface;
use Lmc\Cqrs\Types\ValueObject\CacheKey;
use Lmc\Cqrs\Types\ValueObject\CacheTime;

abstract class AbstractHttpGetQuery extends AbstractHttpQuery implements CacheableInterface
{
    final public function getHttpMethod(): string
    {
        return RequestMethodInterface::METHOD_GET;
    }

    public function getCacheTime(): CacheTime
    {
        return CacheTime::thirtyMinutes();
    }

    public function getCacheKey(): CacheKey
    {
        return new CacheKey(sprintf(
            '%s:%s',
            static::class,
            $this->getUri()
        ));
    }
}
