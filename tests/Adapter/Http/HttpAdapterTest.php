<?php

namespace Sal\Seven\Tests\Adapter\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sal\Seven\Adapter\Http\HttpAdapter;
use Sal\Seven\Model\Http\Authentication\HttpBearerAuthentication;
use Sal\Seven\Model\Http\Header\HttpHeader;
use Sal\Seven\Model\Http\HttpParameter;

class HttpAdapterTest extends TestCase
{
    public function testGet()
    {
        $mockResponse = new Response(200, [], 'ok');

        /** @var Client|MockObject $client */
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('get')
            ->with('https://example.com/test', $this->anything())
            ->willReturn($mockResponse);

        $adapter = new HttpAdapter($client);
        $adapter->setBaseUri('https://example.com');

        $response = $adapter->get('/test');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('ok', (string) $response->getBody());
    }

    public function testPostWithJson()
    {
        $mockResponse = new Response(201, [], 'created');

        /** @var Client|MockObject $client */
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('post')
            ->with('https://example.com/test', $this->callback(function ($options) {
                return isset($options['body']) && '{"key":"value"}' === $options['body'];
            }))
            ->willReturn($mockResponse);

        $adapter = new HttpAdapter($client);
        $adapter->setBaseUri('https://example.com');

        $response = $adapter->post('/test', [], '{"key":"value"}');

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('created', (string) $response->getBody());
    }

    public function testPostWithFormParameters()
    {
        $mockResponse = new Response(200, [], 'ok');

        /** @var Client|MockObject $client */
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('post')
            ->with('https://example.com/test', $this->callback(function ($options) {
                return isset($options['form_params']) && 'bar' === $options['form_params']['foo'];
            }))
            ->willReturn($mockResponse);

        $adapter = new HttpAdapter($client);
        $adapter->setBaseUri('https://example.com');

        $param = new HttpParameter('foo', 'bar');

        $response = $adapter->post('/test', [$param]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('ok', (string) $response->getBody());
    }

    public function testBearerAuthenticationHeader()
    {
        $mockResponse = new Response(200, [], 'ok');

        /** @var Client|MockObject $client */
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('get')
            ->with('https://example.com/test', $this->callback(function ($options) {
                return isset($options['headers']['Authorization'])
                       && 'Bearer TOKEN123' === $options['headers']['Authorization'];
            }))
            ->willReturn($mockResponse);

        $adapter = new HttpAdapter($client);
        $adapter->setBaseUri('https://example.com');
        $adapter->setAuthorization(new HttpBearerAuthentication('TOKEN123'));

        $response = $adapter->get('/test');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('ok', (string) $response->getBody());
    }

    public function testHeadersAreAdded()
    {
        $mockResponse = new Response(200, [], 'ok');

        /** @var Client|MockObject $client */
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('get')
            ->with('https://example.com/test', $this->callback(function ($options) {
                return isset($options['headers']['X-Test']) && 'value' === $options['headers']['X-Test'];
            }))
            ->willReturn($mockResponse);

        $adapter = new HttpAdapter($client);
        $adapter->setBaseUri('https://example.com');
        $adapter->addHeader(new HttpHeader('X-Test', 'value'));

        $response = $adapter->get('/test');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('ok', (string) $response->getBody());
    }
}
