<?php

namespace Sal\Seven\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Sal\Seven\Factory\HttpHeaderFactory;
use Sal\Seven\Model\CharSet;
use Sal\Seven\Model\ContentType;

class HttpHeaderFactoryTest extends TestCase
{
    public function testAccept(): void
    {
        $header = HttpHeaderFactory::accept(ContentType::JSON);
        $this->assertEquals('application/json', $header->getValue());
    }

    public function testContentType(): void
    {
        $header = HttpHeaderFactory::contentType(ContentType::JSON);
        $this->assertEquals('application/json', $header->getValue());
    }

    public function testContentTypeWithCharset(): void
    {
        $header = HttpHeaderFactory::contentType(ContentType::JSON, CharSet::UTF8);
        $this->assertEquals('application/json; charset=utf-8', $header->getValue());
    }

    public function testXRequestedBy(): void
    {
        $header = HttpHeaderFactory::xRequestedBy('test');
        $this->assertEquals('test', $header->getValue());
    }

    public function testUserAgent(): void
    {
        $header = HttpHeaderFactory::userAgent('test');
        $this->assertEquals('test', $header->getValue());
    }
}
