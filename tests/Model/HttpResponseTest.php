<?php

namespace Sal\Clientify\Tests\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Sal\Clientify\Model\Http\HttpResponse;

class HttpResponseTest extends TestCase
{
    public function testParseJsonNull(): void
    {
        $response = new HttpResponse(200, null);
        $this->assertNull($response->parseJson());
    }

    public function testParseJsonInvalid(): void
    {
        $response = new HttpResponse(200);
        $this->assertNull($response->parseJson());
    }

    public function testGetStatusCode()
    {
        $response = new HttpResponse(200);
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGetBody()
    {
        /** @var StreamInterface|MockObject $stream */
        $stream = $this->createMock(StreamInterface::class);
        $response = new HttpResponse(200, $stream);
        $this->assertSame($stream, $response->getBody());
    }

    public function testIsSuccessful()
    {
        $response = new HttpResponse(200);
        $this->assertTrue($response->isSuccessful());

        $response = new HttpResponse(404);
        $this->assertFalse($response->isSuccessful());
    }

    public function testParseJsonReturnsArray()
    {
        /** @var StreamInterface|MockObject $stream */
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('{"foo":"bar"}');

        $response = new HttpResponse(200, $stream);
        $this->assertSame(['foo' => 'bar'], $response->parseJson());
    }

    public function testParseJsonReturnsNullIfBodyIsNull()
    {
        $response = new HttpResponse(200, null);
        $this->assertNull($response->parseJson());
    }

    public function testParseJsonThrowsExceptionOnInvalidJson()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid JSON response.');

        /** @var StreamInterface|MockObject $stream */
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('not-json');

        $response = new HttpResponse(200, $stream);
        $response->parseJson();
    }
}
