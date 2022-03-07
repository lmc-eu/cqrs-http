<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Decoder;

use Lmc\Cqrs\Types\Decoder\ResponseDecoderInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @phpstan-implements ResponseDecoderInterface<MessageInterface, StreamInterface|MessageInterface>
 */
class HttpMessageResponseDecoder implements ResponseDecoderInterface
{
    public function supports(mixed $response, mixed $initiator): bool
    {
        return $response instanceof MessageInterface;
    }

    /**
     * @return StreamInterface|MessageInterface
     */
    public function decode(mixed $response): mixed
    {
        return $response instanceof MessageInterface
            ? $response->getBody()
            : $response;
    }
}
