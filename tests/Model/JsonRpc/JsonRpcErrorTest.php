<?php

namespace Sal\Seven\Tests\Model\JsonRpc;

use PHPUnit\Framework\TestCase;
use Sal\Seven\Model\JsonRpc\JsonRpcError;

class JsonRpcErrorTest extends TestCase
{
    public function testGetters(): void
    {
        $error = new JsonRpcError(
            code: 123,
            message: 'Something went wrong',
            data: ['x' => 1]
        );

        $this->assertSame(123, $error->getCode());
        $this->assertSame('Something went wrong', $error->getMessage());
        $this->assertSame(['x' => 1], $error->getData());
    }

    public function testGettersWithNullData(): void
    {
        $error = new JsonRpcError(
            code: -1,
            message: 'Error message'
        );

        $this->assertSame(-1, $error->getCode());
        $this->assertSame('Error message', $error->getMessage());
        $this->assertNull($error->getData());
    }
}
