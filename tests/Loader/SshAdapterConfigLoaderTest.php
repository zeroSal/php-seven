<?php

namespace Sal\Seven\Tests\Loader;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Sal\Seven\Loader\SshAdapterConfigLoader;
use Sal\Seven\Model\Config\SshAdapterConfig;
use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\SerializerInterface;

class SshAdapterConfigLoaderTest extends TestCase
{
    public function testReturnsNullIfFileDoesNotExist(): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('does not exist'));

        $loader = new SshAdapterConfigLoader(
            '/nonexistent.yml',
            $serializer,
            $logger
        );

        $this->assertNull($loader->load());
    }

    public function testReturnsConfigOnSuccessfulDeserialize(): void
    {
        $path = tempnam(sys_get_temp_dir(), 't');
        file_put_contents($path, "options:\n  - a\n");

        $serializer = $this->createMock(SerializerInterface::class);

        $config = new SshAdapterConfig(['a']);

        $serializer->method('deserialize')
            ->with($this->stringContains('options'), SshAdapterConfig::class, 'yaml')
            ->willReturn($config);

        $loader = new SshAdapterConfigLoader($path, $serializer);

        $this->assertSame($config, $loader->load());

        unlink($path);
    }

    public function testReturnsNullOnDeserializeException(): void
    {
        $path = tempnam(sys_get_temp_dir(), 't');
        file_put_contents($path, "broken:\n  - x\n");

        $serializer = $this->createMock(SerializerInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $serializer->method('deserialize')
            ->willThrowException(
                new BadMethodCallException()
            );

        $logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Unable to deserialize'));

        $loader = new SshAdapterConfigLoader($path, $serializer, $logger);

        $this->assertNull($loader->load());

        unlink($path);
    }
}
