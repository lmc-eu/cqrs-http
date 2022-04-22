<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Exception;

use Lmc\Cqrs\Types\Exception\CqrsExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpBadRequestException extends \RuntimeException implements CqrsExceptionInterface
{
    private static function createMessage(?string $reasonPhrase): string
    {
        return empty($reasonPhrase)
            ? 'HTTP Bad Request'
            : $reasonPhrase;
    }

    public function __construct(
        private RequestInterface $request,
        private ResponseInterface $response,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(self::createMessage($response->getReasonPhrase()), $code, $previous);
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
