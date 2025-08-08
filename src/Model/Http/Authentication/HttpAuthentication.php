<?php

namespace Sal\Seven\Model\Http\Authentication;

/**
 * @author Luca Saladino <sal65535@protonmail.com>
 */
abstract class HttpAuthentication
{
    public function __construct(
        private HttpAuthenticationType $type,
    ) {
    }

    public function getType(): HttpAuthenticationType
    {
        return $this->type;
    }

    public function setType(HttpAuthenticationType $type): void
    {
        $this->type = $type;
    }
}
