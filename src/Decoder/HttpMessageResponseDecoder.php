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
    public function supports($response, $initiator): bool
    {
        return $response instanceof MessageInterface;
    }

    /**
     * @param mixed $response
     * @return StreamInterface|MessageInterface
     */
    public function decode($response)
    {
        return $response instanceof MessageInterface
            ? $response->getBody()
            : $response;
    }
}
