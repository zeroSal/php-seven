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
        private readonly int $statusCode = 0,
        private readonly ?StreamInterface $body = null,
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

    /**
     * Returns the parsed JSON response as an associative array.
     * Returns null if the body is null.
     *
     * @return mixed[]
     *
     * @throws \RuntimeException if the response is not a valid JSON
     */
    public function parseJson(): ?array
    {
        $content = $this->body?->getContents();
        if (null === $content) {
            return null;
        }

        $array = json_decode($content, true);
        if (!is_array($array)) {
            throw new \RuntimeException('Invalid JSON response.');
        }

        return $array;
    }
}
