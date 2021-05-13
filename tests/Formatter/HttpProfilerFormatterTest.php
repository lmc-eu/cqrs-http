<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Formatter;

use GuzzleHttp\Psr7\Utils;
use Lmc\Cqrs\Http\AbstractHttpTestCase;
use Lmc\Cqrs\Types\ValueObject\FormattedValue;
use Lmc\Cqrs\Types\ValueObject\ProfilerItem;

class HttpProfilerFormatterTest extends AbstractHttpTestCase
{
    private HttpProfilerFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new HttpProfilerFormatter();
    }

    /**
     * @dataProvider provideProfilerItem
     *
     * @test
     */
    public function shouldProfileItem(ProfilerItem $item, ProfilerItem $expected): void
    {
        $formatted = $this->formatter->formatItem($item);

        $this->assertEquals($expected, $formatted);
    }

    public function provideProfilerItem(): iterable
    {
        yield 'without anything to format' => [
            new ProfilerItem('id', null, 'test'),
            new ProfilerItem('id', null, 'test'),
        ];

        yield from $this->provideNyholmItems();
        yield from $this->provideNyholmAdditioanlItems();

        yield from $this->provideGuzzleItems();
        yield from $this->provideGuzzleAdditionalItems();
    }

    public function provideNyholmItems(): iterable
    {
        yield 'with Nyholm request with body' => [
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                $request = (new \Nyholm\Psr7\Request('get', 'url'))
                    ->withBody(\Nyholm\Psr7\Stream::create('body'))
            ),
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                new FormattedValue($request, 'body')
            ),
        ];

        yield 'with Nyholm request without body' => [
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                $request = (new \Nyholm\Psr7\Request('get', 'url'))
            ),
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                new FormattedValue($request, '')
            ),
        ];

        yield 'with Nyholm response' => [
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                $response = (new \Nyholm\Psr7\Response(200, [], 'body'))
            ),
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                new FormattedValue($response, 'body')
            ),
        ];

        yield 'with Nyholm response with stream' => [
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                $response = (new \Nyholm\Psr7\Response(200, [], \Nyholm\Psr7\Stream::create('body')))
            ),
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                new FormattedValue($response, 'body')
            ),
        ];

        yield 'with Nyholm stream' => [
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                $stream = (\Nyholm\Psr7\Stream::create('body'))
            ),
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                new FormattedValue($stream, 'body')
            ),
        ];
    }

    public function provideNyholmAdditioanlItems(): iterable
    {
        yield 'with additional Nyholm request with body' => [
            new ProfilerItem(
                'id',
                [
                    'additional' => $request = (new \Nyholm\Psr7\Request('get', 'url'))
                        ->withBody(\Nyholm\Psr7\Stream::create('body')),
                ],
                'test'
            ),
            new ProfilerItem(
                'id',
                [
                    'additional' => new FormattedValue($request, 'body'),
                ],
                'test'
            ),
        ];

        yield 'with additional Nyholm request without body' => [
            new ProfilerItem(
                'id',
                [
                    'additional' => $request = (new \Nyholm\Psr7\Request('get', 'url')),
                ],
                'test',
            ),
            new ProfilerItem(
                'id',
                [
                    'additional' => new FormattedValue($request, ''),
                ],
                'test',
            ),
        ];

        yield 'with additional Nyholm response' => [
            new ProfilerItem(
                'id',
                [
                    'additional' => $response = (new \Nyholm\Psr7\Response(200, [], 'body')),
                ],
                'test',
            ),
            new ProfilerItem(
                'id',
                [
                    'additional' => new FormattedValue($response, 'body'),
                ],
                'test',
            ),
        ];

        yield 'with additional Nyholm response with stream' => [
            new ProfilerItem(
                'id',
                [
                    'additional' => $response = (new \Nyholm\Psr7\Response(
                        200,
                        [],
                        \Nyholm\Psr7\Stream::create('body')
                    )),
                ],
                'test'
            ),
            new ProfilerItem(
                'id',
                [
                    'additional' => new FormattedValue($response, 'body'),
                ],
                'test'
            ),
        ];

        yield 'with additional Nyholm stream' => [
            new ProfilerItem(
                'id',
                [
                    'additional' => $stream = (\Nyholm\Psr7\Stream::create('body')),
                ],
                'test',
            ),
            new ProfilerItem(
                'id',
                [
                    'additional' => new FormattedValue($stream, 'body'),
                ],
                'test',
            ),
        ];
    }

    public function provideGuzzleItems(): iterable
    {
        yield 'with Guzzle request with body' => [
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                $request = (new \GuzzleHttp\Psr7\Request('post', 'url'))
                    ->withBody(Utils::streamFor('body'))
            ),
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                new FormattedValue($request, 'body')
            ),
        ];

        yield 'with Guzzle request without body' => [
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                $request = (new \GuzzleHttp\Psr7\Request('post', 'url'))
            ),
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                new FormattedValue($request, '')
            ),
        ];

        yield 'with Guzzle response' => [
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                $response = (new \GuzzleHttp\Psr7\Response(200, [], 'body'))
            ),
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                new FormattedValue($response, 'body')
            ),
        ];

        yield 'with Guzzle response with stream' => [
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                $response = (new \GuzzleHttp\Psr7\Response(200, [], Utils::streamFor('body')))
            ),
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                new FormattedValue($response, 'body')
            ),
        ];

        yield 'with Guzzle stream' => [
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                $stream = (Utils::streamFor('body'))
            ),
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                new FormattedValue($stream, 'body')
            ),
        ];
    }

    public function provideGuzzleAdditionalItems(): iterable
    {
        yield 'with additional Guzzle request with body' => [
            new ProfilerItem(
                'id',
                [
                    'additional' => $request = (new \GuzzleHttp\Psr7\Request('post', 'url'))
                        ->withBody(Utils::streamFor('body')),
                ],
                'test',
            ),
            new ProfilerItem(
                'id',
                [
                    'additional' => new FormattedValue($request, 'body'),
                ],
                'test'
            ),
        ];

        yield 'with additional Guzzle request without body' => [
            new ProfilerItem(
                'id',
                [
                    'additional' => $request = (new \GuzzleHttp\Psr7\Request('post', 'url')),
                ],
                'test',
            ),
            new ProfilerItem(
                'id',
                [
                    'additional' => new FormattedValue($request, ''),
                ],
                'test'
            ),
        ];

        yield 'with additional Guzzle response' => [
            new ProfilerItem(
                'id',
                [
                    'additional' => $response = (new \GuzzleHttp\Psr7\Response(200, [], 'body')),
                ],
                'test',
            ),
            new ProfilerItem(
                'id',
                [
                    'additional' => new FormattedValue($response, 'body'),
                ],
                'test',
            ),
        ];

        yield 'with additional Guzzle response with stream' => [
            new ProfilerItem(
                'id',
                [
                    'additional' => $response = (new \GuzzleHttp\Psr7\Response(200, [], Utils::streamFor('body'))),
                ],
                'test',
            ),
            new ProfilerItem(
                'id',
                [
                    'additional' => new FormattedValue($response, 'body'),
                ],
                'test',
            ),
        ];

        yield 'with additional Guzzle stream' => [
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                $stream = (Utils::streamFor('body'))
            ),
            new ProfilerItem(
                'id',
                null,
                'test',
                '',
                new FormattedValue($stream, 'body')
            ),
        ];
    }
}
