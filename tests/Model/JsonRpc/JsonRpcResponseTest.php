<?php

namespace Sal\Seven\Tests\Model\JsonRpc;

use PHPUnit\Framework\TestCase;
use Sal\Seven\Model\JsonRpc\JsonRpcError;
use Sal\Seven\Model\JsonRpc\JsonRpcResponse;

class JsonRpcResponseTest extends TestCase
{
    public function testSuccessfulResponse(): void
    {
        $response = new JsonRpcResponse(
            id: '123',
            result: ['ok' => true],
            error: null
        );

        $this->assertSame('123', $response->getId());
        $this->assertSame(['ok' => true], $response->getResult());
        $this->assertNull($response->getError());
        $this->assertTrue($response->isSuccessful());
    }

    public function testErrorResponse(): void
    {
        $error = new JsonRpcError(
            code: -32600,
            message: 'Invalid request'
        );

        $response = new JsonRpcResponse(
            id: null,
            result: null,
            error: $error
        );

        $this->assertNull($response->getId());
        $this->assertNull($response->getResult());
        $this->assertSame($error, $response->getError());
        $this->assertFalse($response->isSuccessful());
    }
}
