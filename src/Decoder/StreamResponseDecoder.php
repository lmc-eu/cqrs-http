<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Decoder;

use Lmc\Cqrs\Types\Decoder\ResponseDecoderInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @phpstan-implements ResponseDecoderInterface<StreamInterface, StreamInterface|string>
 */
class StreamResponseDecoder implements ResponseDecoderInterface
{
    public function supports(mixed $response, mixed $initiator): bool
    {
        return $response instanceof StreamInterface;
    }

    /**
     * @return StreamInterface|string
     */
    public function decode(mixed $response): mixed
    {
        return $response instanceof StreamInterface
            ? $this->decodeStream($response)
            : $response;
    }

    private function decodeStream(StreamInterface $stream): StreamInterface|string
    {
        try {
            if (!empty($contents = $stream->getContents())) {
                return $contents;
            }

            if (!empty($contents = (string) $stream)) {
                return $contents;
            }

            if (($size = $stream->getSize()) > 0 && $stream->isReadable() && !empty($contents = $stream->read($size))) {
                return $contents;
            }

            if ($stream->getSize() === 0) {
                return '';
            }

            return $stream;
        } catch (\Throwable $e) {
            return $stream;
        }
    }
}
