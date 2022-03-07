<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Handler;

use Lmc\Cqrs\Http\Exception\HttpBadRequestException;
use Lmc\Cqrs\Http\Exception\HttpServerErrorException;
use Lmc\Cqrs\Types\Base\AbstractSendCommandHandler;
use Lmc\Cqrs\Types\CommandInterface;
use Lmc\Cqrs\Types\ValueObject\OnErrorInterface;
use Lmc\Cqrs\Types\ValueObject\OnSuccessInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @phpstan-extends AbstractSendCommandHandler<RequestInterface, ResponseInterface>
 */
class HttpSendCommandHandler extends AbstractSendCommandHandler
{
    public function __construct(private ClientInterface $client)
    {
    }

    /** @phpstan-param CommandInterface<mixed> $command */
    public function supports(CommandInterface $command): bool
    {
        return $command->getRequestType() === RequestInterface::class;
    }

    /**
     * @phpstan-param CommandInterface<RequestInterface> $command
     * @phpstan-param OnSuccessInterface<ResponseInterface> $onSuccess
     */
    public function handle(CommandInterface $command, OnSuccessInterface $onSuccess, OnErrorInterface $onError): void
    {
        if (!$this->assertIsSupported(RequestInterface::class, $command, $onError)) {
            return;
        }

        try {
            $request = $command->createRequest();
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
