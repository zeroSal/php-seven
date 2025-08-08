<?php

namespace Sal\Seven\Model\Http\Authentication;

/**
 * @author Luca Saladino <sal65535@protonmail.com>
 */
class HttpBasicAuthentication extends HttpAuthentication
{
    public function __construct(
        private string $username,
        private string $password,
    ) {
        parent::__construct(HttpAuthenticationType::BASIC);
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
