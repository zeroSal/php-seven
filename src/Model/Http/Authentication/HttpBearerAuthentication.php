<?php

namespace Sal\Clientify\Model\Http\Authentication;

/**
 * @author Luca Saladino <sal65535@protonmail.com>
 */
class HttpBearerAuthentication extends HttpAuthentication
{
    public function __construct(
        private string $token,
    ) {
        parent::__construct(HttpAuthenticationType::BEARER);
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }
}
