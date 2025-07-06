<?php

namespace Sal\Clientify\Model\Http;

use Psr\Http\Message\StreamInterface;

/**
 * @author Luca Saladino <sal65535@protonmail.com>
 *
 * @final
 */
class HttpResponse
{
    public function __construct(
        private int $statusCode = 0,
        private ?StreamInterface $body = null,
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getBody(): ?StreamInterface
    {
        return $this->body;
    }

    public function isSuccessful(): bool
    {
        return $this->statusCode < 400;
    }
}
