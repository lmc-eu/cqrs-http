<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Decoder;

use GuzzleHttp\Psr7\Utils;
use Lmc\Cqrs\Http\AbstractHttpTestCase;

class StreamResponseDecoderTest extends AbstractHttpTestCase
{
    private StreamResponseDecoder $decoder;

    protected function setUp(): void
    {
        $this->decoder = new StreamResponseDecoder();
    }

    /**
     * @dataProvider provideMessage
     * @test
     */
    public function shouldDecodeNyholmMessage(mixed $message, bool $isSupported, mixed $expected): void
    {
        $decoded = $this->decoder->decode($message);

        $this->assertSame($isSupported, $this->decoder->supports($message, null));
        $this->assertEquals($expected, $decoded);
    }

    public function provideMessage(): iterable
    {
        yield 'not a stream' => [
            $obj = new \stdClass(),
            false,
            $obj,
        ];

        yield from $this->provideNyholmMessage();
        yield from $this->provideGuzzleMessage();
    }

    public function provideNyholmMessage(): iterable
    {
        yield 'Nyholm stream' => [
            \Nyholm\Psr7\Stream::create('body'),
            true,
            'body',
        ];
    }

    public function provideGuzzleMessage(): iterable
    {
        yield 'Guzzle stream' => [
            Utils::streamFor('body'),
            true,
            'body',
        ];
    }
}
