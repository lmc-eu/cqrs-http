<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Decoder;

use Lmc\Cqrs\Types\Decoder\ResponseDecoderInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @phpstan-implements ResponseDecoderInterface<StreamInterface, StreamInterface|string>
 */
class StreamResponseDecoder implements ResponseDecoderInterface
{
    public function supports($response): bool
    {
        return $response instanceof StreamInterface;
    }

    /**
     * @param mixed $response
     * @return StreamInterface|string
     */
    public function decode($response)
    {
        return $response instanceof StreamInterface
            ? $this->decodeStream($response)
            : $response;
    }

    /** @return StreamInterface|string */
    private function decodeStream(StreamInterface $stream)
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
