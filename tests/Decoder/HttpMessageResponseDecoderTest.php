<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Decoder;

use GuzzleHttp\Psr7\Utils;
use Lmc\Cqrs\Http\AbstractHttpTestCase;

class HttpMessageResponseDecoderTest extends AbstractHttpTestCase
{
    private HttpMessageResponseDecoder $decoder;

    protected function setUp(): void
    {
        $this->decoder = new HttpMessageResponseDecoder();
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
        yield 'not a message' => [
            $obj = new \stdClass(),
            false,
            $obj,
        ];

        yield from $this->provideNyholmMessage();
        yield from $this->provideGuzzleMessage();
    }

    public function provideNyholmMessage(): iterable
    {
        yield 'request Nyholm with body' => [
            (new \Nyholm\Psr7\Request('post', 'url'))->withBody($stream = \Nyholm\Psr7\Stream::create('body')),
            true,
            $stream,
        ];

        yield 'Nyholm response' => [
            new \Nyholm\Psr7\Response(200, [], 'body'),
            true,
            'body',
        ];

        yield 'Nyholm response with stream' => [
            new \Nyholm\Psr7\Response(200, [], \Nyholm\Psr7\Stream::create('body')),
            true,
            'body',
        ];
    }

    public function provideGuzzleMessage(): iterable
    {
        yield 'Guzzle request with body' => [
            (new \GuzzleHttp\Psr7\Request('post', 'url'))->withBody($stream = Utils::streamFor('body')),
            true,
            $stream,
        ];

        yield 'Guzzle response' => [
            new \GuzzleHttp\Psr7\Response(200, [], 'body'),
            true,
            'body',
        ];

        yield 'Guzzle response with stream' => [
            new \GuzzleHttp\Psr7\Response(200, [], Utils::streamFor('body')),
            true,
            'body',
        ];
    }
}
