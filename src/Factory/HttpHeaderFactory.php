<?php

namespace Sal\Clientify\Factory;

use Sal\Clientify\Model\Http\Header\HttpHeader;

/**
 * @author Luca Saladino <sal65535@protonmail.com>
 */
class HttpHeaderFactory
{
    public static function acceptJson(): HttpHeader
    {
        return new HttpHeader('Accept', 'application/json');
    }
}
