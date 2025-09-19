<?php

namespace Sal\Seven\Tests\Adapter\Ssh;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Sal\Seven\Adapter\Ssh\SshAdapter;
use Symfony\Component\Console\Logger\ConsoleLogger;

class SshAdapterTest extends TestCase
{
    public function testSetLogger()
    {
        $adapter = new SshAdapter();
        /** @var LoggerInterface|MockObject */
        $logger = $this->createMock(ConsoleLogger::class);
        $adapter->setLogger($logger);
        $this->assertInstanceOf(ConsoleLogger::class, $logger);

        /** @var LoggerInterface|MockObject */
        $logger = $this->createMock(NullLogger::class);
        $adapter->setLogger($logger);
        $this->assertInstanceOf(NullLogger::class, $logger);
    }

    public function testSetTimeout()
    {
        $adapter = new SshAdapter();
        $adapter->setTimeout(120);
        $this->assertEquals(120, $adapter->getTimeout());
        $this->assertContains('ConnectTimeout=120', $adapter->getOptions());

        $adapter->setTimeout(null);
        $this->assertNull($adapter->getTimeout());
        $this->assertContains('ConnectTimeout=0', $adapter->getOptions());
    }

    public function testSetters()
    {
        $adapter = new SshAdapter();
        $adapter->setHost('127.0.0.1');
        $adapter->setUser('admin');
        $adapter->setTimeout(30);

        $this->assertEquals('127.0.0.1', $adapter->getHost());
        $this->assertEquals('admin', $adapter->getUser());
        $this->assertEquals(30, $adapter->getTimeout());
        $this->assertContains('-o', $adapter->getOptions());
    }

    public function testAddIdentityFile()
    {
        $adapter = new SshAdapter();
        $adapter->addIdentityFile('/path/to/id_rsa');
        $this->assertContains('-i', $adapter->getOptions());
        $this->assertContains('/path/to/id_rsa', $adapter->getOptions());
    }

    public function testAddJump()
    {
        $adapter = new SshAdapter();
        $adapter->addJump('jump.example.com');
        $this->assertContains('-J', $adapter->getOptions());
        $this->assertContains('jump.example.com', $adapter->getOptions());
    }

    public function testAddConfigFile()
    {
        $adapter = new SshAdapter();
        $adapter->addConfigFile('/path/to/ssh_config');
        $this->assertContains('-F', $adapter->getOptions());
        $this->assertContains('/path/to/ssh_config', $adapter->getOptions());
    }

    public function testAddOption()
    {
        $adapter = new SshAdapter();
        $adapter->addOption('test');
        $this->assertContains('-o', $adapter->getOptions());
        $this->assertContains('test', $adapter->getOptions());
    }

    public function testPermitDsaHostKey()
    {
        $adapter = new SshAdapter();
        $options = $adapter->getOptions();

        $this->assertNotContains('HostKeyAlgorithms=+ssh-dss', $options);

        $adapter->permitDsaHostKey(true);
        $options = $adapter->getOptions();
        $this->assertContains('HostKeyAlgorithms=+ssh-dss', $options);

        $adapter->permitDsaHostKey(false);
        $options = $adapter->getOptions();
        $this->assertNotContains('HostKeyAlgorithms=+ssh-dss', $options);
    }
}
