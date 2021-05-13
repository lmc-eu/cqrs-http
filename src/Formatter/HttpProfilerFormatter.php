<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Formatter;

use Lmc\Cqrs\Types\Formatter\ProfilerFormatterInterface;
use Lmc\Cqrs\Types\ValueObject\FormattedValue;
use Lmc\Cqrs\Types\ValueObject\ProfilerItem;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class HttpProfilerFormatter implements ProfilerFormatterInterface
{
    public function formatItem(ProfilerItem $item): ProfilerItem
    {
        if (($response = $item->getResponse()) instanceof MessageInterface && ($formatted = $this->formatMessage($response))) {
            $item->setResponse($formatted);
        }
        if (($stream = $item->getResponse()) instanceof StreamInterface && ($formatted = $this->formatStream($stream))) {
            $item->setResponse($formatted);
        }

        foreach ($item->getAdditionalData() as $key => $value) {
            if ($value instanceof MessageInterface && ($formatted = $this->formatMessage($value))) {
                $item->setAdditionalData($key, $formatted);
            }
            if ($value instanceof StreamInterface && ($formatted = $this->formatStream($value))) {
                $item->setAdditionalData($key, $formatted);
            }
        }

        return $item;
    }

    /** @phpstan-return ?FormattedValue<MessageInterface, string> */
    private function formatMessage(MessageInterface $message): ?FormattedValue
    {
        return ($formatted = $this->formatStream($message->getBody()))
            ? new FormattedValue($message, $formatted->getFormatted())
            : null;
    }

    /** @phpstan-return ?FormattedValue<StreamInterface, string> */
    private function formatStream(StreamInterface $stream): ?FormattedValue
    {
        try {
            if (!empty($contents = $stream->getContents())) {
                return new FormattedValue($stream, $contents);
            }

            if (!empty($contents = (string) $stream)) {
                return new FormattedValue($stream, $contents);
            }

            if (($size = $stream->getSize()) > 0 && $stream->isReadable() && !empty($contents = $stream->read($size))) {
                return new FormattedValue($stream, $contents);
            }

            if ($stream->getSize() === 0) {
                return new FormattedValue($stream, '');
            }

            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
