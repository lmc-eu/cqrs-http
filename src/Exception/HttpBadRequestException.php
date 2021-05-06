<?php declare(strict_types=1);

namespace Lmc\Cqrs\Http\Exception;

use Lmc\Cqrs\Types\Exception\CqrsExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpBadRequestException extends \RuntimeException implements CqrsExceptionInterface
{
    private RequestInterface $request;
    private ResponseInterface $response;

    public function __construct(
        RequestInterface $request,
        ResponseInterface $response,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($response->getReasonPhrase() ?? 'HTTP Bad Request', $code, $previous);
        $this->request = $request;
        $this->response = $response;
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
