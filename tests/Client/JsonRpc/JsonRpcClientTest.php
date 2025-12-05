<?php

namespace Sal\Seven\Tests\Client\JsonRpc;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Sal\Seven\Adapter\Http\HttpAdapterInterface;
use Sal\Seven\Client\JsonRpc\JsonRpcClient;
use Sal\Seven\Factory\HttpHeaderFactory;
use Sal\Seven\Model\ContentType;
use Sal\Seven\Model\Http\HttpResponse;
use Sal\Seven\Model\JsonRpc\JsonRpcResponse;
use Symfony\Component\Serializer\SerializerInterface;

class JsonRpcClientTest extends TestCase
{
    /** @var SerializerInterface|MockObject */
    private SerializerInterface $serializer;

    /** @var HttpAdapterInterface|MockObject */
    private HttpAdapterInterface $http;

    private JsonRpcClient $client;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->http = $this->createMock(HttpAdapterInterface::class);

        $this->http
            ->expects($this->once())
            ->method('addHeader')
            ->with(HttpHeaderFactory::contentType(ContentType::JSON));

        $this->client = new JsonRpcClient($this->serializer, $this->http);
    }

    public function testThrowsIfEndpointMissing(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The JSON-RPC endpoint not set.');
        $this->client->call('method');
    }

    public function testThrowsOnInvalidJsonPayload(): void
    {
        $this->client->setEndpoint('http://test');

        $this->client->setAuth("\xB1\x31"); // force json_decode to fail

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid JSON-RPC payload.');
        $this->client->call('method');
    }

    public function testThrowsIfHttpNotSuccessful(): void
    {
        $this->client->setEndpoint('http://test');
        $this->client->setAuth('auth');

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('ERR');

        $httpResponse = $this->createMock(HttpResponse::class);
        $httpResponse->method('isSuccessful')->willReturn(false);
        $httpResponse->method('getStatusCode')->willReturn(500);
        $httpResponse->method('getBody')->willReturn($stream);

        $this->http->method('post')->willReturn($httpResponse);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("500: 'ERR'");

        $this->client->call('test');
    }

    public function testThrowsIfNoBody(): void
    {
        $this->client->setEndpoint('http://test');

        $httpResponse = $this->createMock(HttpResponse::class);
        $httpResponse->method('isSuccessful')->willReturn(true);
        $httpResponse->method('getBody')->willReturn(null);

        $this->http->method('post')->willReturn($httpResponse);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No JSON-RPC response received.');

        $this->client->call('test');
    }

    public function testThrowsIfInvalidResponseObject(): void
    {
        $this->client->setEndpoint('http://test');

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('{"ok":1}');

        $httpResponse = $this->createMock(HttpResponse::class);
        $httpResponse->method('isSuccessful')->willReturn(true);
        $httpResponse->method('getBody')->willReturn($stream);

        $this->http->method('post')->willReturn($httpResponse);
        $this->serializer->method('deserialize')->willReturn(new \stdClass());

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid JSON-RPC response.');

        $this->client->call('test');
    }

    public function testSuccessfulCall(): void
    {
        $this->client->setEndpoint('http://test');
        $this->client->setAuth('auth');

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('{"result":123}');

        $httpResponse = $this->createMock(HttpResponse::class);
        $httpResponse->method('isSuccessful')->willReturn(true);
        $httpResponse->method('getBody')->willReturn($stream);

        $this->http->method('post')->willReturn($httpResponse);

        $expected = $this->createMock(JsonRpcResponse::class);

        $this->serializer
            ->method('deserialize')
            ->with('{"result":123}', JsonRpcResponse::class, 'json')
            ->willReturn($expected);

        $out = $this->client->call('method', ['a' => 1]);

        $this->assertSame($expected, $out);
    }
}
