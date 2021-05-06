<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Handler;

use Lmc\Cqrs\Http\Exception\HttpBadRequestException;
use Lmc\Cqrs\Http\Exception\HttpServerErrorException;
use Lmc\Cqrs\Types\Base\AbstractQueryHandler;
use Lmc\Cqrs\Types\QueryInterface;
use Lmc\Cqrs\Types\ValueObject\OnErrorInterface;
use Lmc\Cqrs\Types\ValueObject\OnSuccessInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @phpstan-extends AbstractQueryHandler<RequestInterface, ResponseInterface>
 */
class HttpQueryHandler extends AbstractQueryHandler
{
    private ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /** @phpstan-param QueryInterface<mixed> $query */
    public function supports(QueryInterface $query): bool
    {
        return $query->getRequestType() === RequestInterface::class;
    }

    /**
     * @phpstan-param QueryInterface<RequestInterface> $query
     * @phpstan-param OnSuccessInterface<ResponseInterface> $onSuccess
     */
    public function handle(QueryInterface $query, OnSuccessInterface $onSuccess, OnErrorInterface $onError): void
    {
        if (!$this->assertIsSupported(RequestInterface::class, $query, $onError)) {
            return;
        }

        try {
            $request = $query->createRequest();
            $response = $this->client->sendRequest($request);

            if ($response->getStatusCode() >= 500) {
                $onError(new HttpServerErrorException($request, $response));
            } elseif ($response->getStatusCode() >= 400) {
                $onError(new HttpBadRequestException($request, $response));
            } else {
                $onSuccess($response);
            }
        } catch (\Throwable $e) {
            $onError($e);
        }
    }
}
