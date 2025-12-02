<?php

namespace Sal\Seven\Tests\Adapter\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Sal\Seven\Adapter\Http\HttpAdapter;
use Sal\Seven\Model\Http\Authentication\HttpBearerAuthentication;
use Sal\Seven\Model\Http\Header\HttpHeader;
use Sal\Seven\Model\Http\HttpParameter;
use Symfony\Component\Console\Logger\ConsoleLogger;

class HttpAdapterTest extends TestCase
{
    public function testSetLogger()
    {
        $adapter = new HttpAdapter(new Client());

        /** @var LoggerInterface|MockObject */
        $logger = $this->createMock(ConsoleLogger::class);
        $adapter->setLogger($logger);
        $this->assertInstanceOf(ConsoleLogger::class, $adapter->getLogger());

        /** @var LoggerInterface|MockObject */
        $logger = $this->createMock(NullLogger::class);
        $adapter->setLogger($logger);
        $this->assertInstanceOf(NullLogger::class, $adapter->getLogger());
    }

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

    public function testDelete()
    {
        $mockResponse = new Response(200, [], 'ok');

        /** @var Client|MockObject $client */
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('delete')
            ->with('https://example.com/test', $this->anything())
            ->willReturn($mockResponse);

        $adapter = new HttpAdapter($client);
        $adapter->setBaseUri('https://example.com');

        $response = $adapter->delete('/test');

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

    public function testPutWithJson()
    {
        $mockResponse = new Response(201, [], 'created');

        /** @var Client|MockObject $client */
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('put')
            ->with('https://example.com/test', $this->callback(function ($options) {
                return isset($options['body']) && '{"key":"value"}' === $options['body'];
            }))
            ->willReturn($mockResponse);

        $adapter = new HttpAdapter($client);
        $adapter->setBaseUri('https://example.com');

        $response = $adapter->put('/test', [], '{"key":"value"}');

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('created', (string) $response->getBody());
    }

    public function testPatchWithJson()
    {
        $mockResponse = new Response(201, [], 'created');

        /** @var Client|MockObject $client */
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('patch')
            ->with('https://example.com/test', $this->callback(function ($options) {
                return isset($options['body']) && '{"key":"value"}' === $options['body'];
            }))
            ->willReturn($mockResponse);

        $adapter = new HttpAdapter($client);
        $adapter->setBaseUri('https://example.com');

        $response = $adapter->patch('/test', [], '{"key":"value"}');

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

    public function testPutWithFormParameters()
    {
        $mockResponse = new Response(200, [], 'ok');

        /** @var Client|MockObject $client */
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('put')
            ->with('https://example.com/test', $this->callback(function ($options) {
                return isset($options['form_params']) && 'bar' === $options['form_params']['foo'];
            }))
            ->willReturn($mockResponse);

        $adapter = new HttpAdapter($client);
        $adapter->setBaseUri('https://example.com');

        $param = new HttpParameter('foo', 'bar');

        $response = $adapter->put('/test', [$param]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('ok', (string) $response->getBody());
    }

    public function testPatchWithFormParameters()
    {
        $mockResponse = new Response(200, [], 'ok');

        /** @var Client|MockObject $client */
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('patch')
            ->with('https://example.com/test', $this->callback(function ($options) {
                return isset($options['form_params']) && 'bar' === $options['form_params']['foo'];
            }))
            ->willReturn($mockResponse);

        $adapter = new HttpAdapter($client);
        $adapter->setBaseUri('https://example.com');

        $param = new HttpParameter('foo', 'bar');

        $response = $adapter->patch('/test', [$param]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('ok', (string) $response->getBody());
    }

    public function testUploadFile(): void
    {
        $mockResponse = new Response(201, [], 'uploaded');

        $filePath = sys_get_temp_dir().'/test_upload.txt';
        file_put_contents($filePath, 'test content');
        $file = new \SplFileInfo($filePath);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('post')
            ->with(
                'https://example.com/upload',
                $this->callback(
                    function (mixed $options) use ($file): bool {
                        return isset($options['body'])
                            && stream_get_contents($options['body']) === file_get_contents($file->getPathname());
                    }
                )
            )
            ->willReturn($mockResponse);

        $adapter = new HttpAdapter($client);
        $adapter->setBaseUri('https://example.com');

        $response = $adapter->upload('/upload', $file);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('uploaded', (string) $response->getBody());

        unlink($filePath);
    }

    public function testReplaceFile(): void
    {
        $mockResponse = new Response(200, [], 'replaced');

        $filePath = sys_get_temp_dir().'/test_replace.txt';
        file_put_contents($filePath, 'replace content');
        $file = new \SplFileInfo($filePath);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('put')
            ->with(
                'https://example.com/replace',
                $this->callback(
                    function (mixed $options) use ($file): bool {
                        return isset($options['body'])
                            && stream_get_contents($options['body']) === file_get_contents($file->getPathname());
                    }
                )
            )
            ->willReturn($mockResponse);

        $adapter = new HttpAdapter($client);
        $adapter->setBaseUri('https://example.com');

        $response = $adapter->replace('/replace', $file);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('replaced', (string) $response->getBody());

        unlink($filePath);
    }

    public function testUploadFileThrowsExceptionIfFileCannotBeOpened(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot open file');

        $file = new \SplFileInfo('/path/to/nonexistent/file.txt');

        $client = $this->createMock(Client::class);
        $adapter = new HttpAdapter($client);

        $adapter->upload('/upload', $file);
    }

    public function testReplaceFileThrowsExceptionIfFileCannotBeOpened(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot open file');

        $file = new \SplFileInfo('/path/to/nonexistent/file.txt');

        $client = $this->createMock(Client::class);
        $adapter = new HttpAdapter($client);

        $adapter->replace('/replace', $file);
    }

    public function testUploadFileWithGuzzleException(): void
    {
        $this->expectException(\GuzzleHttp\Exception\GuzzleException::class);

        $filePath = sys_get_temp_dir().'/test_upload.txt';
        file_put_contents($filePath, 'test content');
        $file = new \SplFileInfo($filePath);

        $client = $this->createMock(Client::class);
        $client->method('post')->willThrowException(
            new class extends \Exception implements \GuzzleHttp\Exception\GuzzleException {}
        );

        $adapter = new HttpAdapter($client);

        $adapter->upload('/upload', $file);

        unlink($filePath);
    }

    public function testReplaceFileWithGuzzleException(): void
    {
        $this->expectException(\GuzzleHttp\Exception\GuzzleException::class);

        $filePath = sys_get_temp_dir().'/test_replace.txt';
        file_put_contents($filePath, 'replace content');
        $file = new \SplFileInfo($filePath);

        $client = $this->createMock(Client::class);
        $client->method('put')->willThrowException(
            new class extends \Exception implements \GuzzleHttp\Exception\GuzzleException {}
        );

        $adapter = new HttpAdapter($client);

        $adapter->replace('/replace', $file);

        unlink($filePath);
    }

    public function testBearerAuthenticationHeader(): void
    {
        $mockResponse = new Response(200, [], 'ok');

        /** @var Client|MockObject $client */
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('get')
            ->with(
                'https://example.com/test',
                $this->callback(
                    function (mixed $options): bool {
                        return isset($options['headers']['Authorization'])
                            && 'Bearer TOKEN123' === $options['headers']['Authorization'];
                    }
                )
            )
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

    public function testAddStrictResolve()
    {
        $adapter = new HttpAdapter(new Client());

        $adapter->addStrictResolve('example.com', 8080, '127.0.0.1');
        $adapter->addStrictResolve('example2.com', 8082, '127.0.0.2');

        $list = $adapter->getStrictResolveList();
        $this->assertCount(2, $list);
        $this->assertEquals('example.com:8080:127.0.0.1', $list[0]);
        $this->assertEquals('example2.com:8082:127.0.0.2', $list[1]);
    }

    public function testSetThrowOnError()
    {
        $adapter = new HttpAdapter(new Client());
        $this->assertFalse($adapter->doesThrowOnError());

        $adapter->setThrowOnError(true);
        $this->assertTrue($adapter->doesThrowOnError());

        $adapter->setThrowOnError(false);
        $this->assertFalse($adapter->doesThrowOnError());
    }

    public function testSetAuthorization()
    {
        $adapter = new HttpAdapter(new Client());
        $this->assertNull($adapter->getAuthorization());

        $auth = new HttpBearerAuthentication('token');
        $adapter->setAuthorization($auth);
        $this->assertEquals($auth, $adapter->getAuthorization());

        $adapter->setAuthorization(null);
        $this->assertNull($adapter->getAuthorization());
    }

    public function testIsAuthorized()
    {
        $adapter = new HttpAdapter(new Client());
        $this->assertFalse($adapter->isAuthorized());

        $auth = new HttpBearerAuthentication('token');
        $adapter->setAuthorization($auth);
        $this->assertTrue($adapter->isAuthorized());

        $adapter->setAuthorization(null);
        $this->assertFalse($adapter->isAuthorized());
    }

    public function testIsVerify()
    {
        $adapter = new HttpAdapter(new Client());
        $this->assertFalse($adapter->isVerify());

        $adapter->setVerify(true);
        $this->assertTrue($adapter->isVerify());

        $adapter->setVerify(false);
        $this->assertFalse($adapter->isVerify());
    }

    public function testSetHeaders()
    {
        $adapter = new HttpAdapter(new Client());
        $adapter->setHeaders([]);
        $this->assertEmpty($adapter->getHeaders());

        $header1 = new HttpHeader('X-Test1', 'value1');
        $header2 = new HttpHeader('X-Test2', 'value2');
        $adapter->setHeaders([$header1, $header2]);
        $this->assertCount(2, $adapter->getHeaders());
    }

    public function testAddHeader()
    {
        $adapter = new HttpAdapter(new Client());
        $adapter->setHeaders([new HttpHeader('X-Test1', 'value1')]);
        $adapter->addHeader(new HttpHeader('X-Test', 'value'));
        $this->assertCount(2, $adapter->getHeaders());
    }

    public function testRemoveHeaderByName()
    {
        $adapter = new HttpAdapter(new Client());
        $header1 = new HttpHeader('X-Test1', 'value1');
        $header2 = new HttpHeader('X-Test2', 'value2');
        $header3 = new HttpHeader('X-Test3', 'value3');
        $adapter->setHeaders([$header1, $header2, $header3]);
        $this->assertCount(3, $adapter->getHeaders());

        $adapter->removeHeaderByName('X-Test2');
        $this->assertCount(2, $adapter->getHeaders());

        // Removing a non-existing header should not change anything
        $adapter->removeHeaderByName('X-Non-Existing');
        $this->assertCount(2, $adapter->getHeaders());
    }

    public function testRemoveHeader()
    {
        $adapter = new HttpAdapter(new Client());
        $header1 = new HttpHeader('X-Test1', 'value1');
        $header2 = new HttpHeader('X-Test2', 'value2');
        $header3 = new HttpHeader('X-Test3', 'value3');
        $adapter->setHeaders([$header1, $header2, $header3]);
        $this->assertCount(3, $adapter->getHeaders());

        $adapter->removeHeader($header2);
        $this->assertCount(2, $adapter->getHeaders());

        // Removing a non-existing header should not change anything
        $adapter->removeHeader(new HttpHeader('X-Non-Existing', 'value'));
        $this->assertCount(2, $adapter->getHeaders());
    }

    public function testSetBaseUri()
    {
        $adapter = new HttpAdapter(new Client());
        $this->assertNull($adapter->getBaseUri());

        $adapter->setBaseUri('https://example.com');
        $this->assertEquals('https://example.com', $adapter->getBaseUri());
    }
}
