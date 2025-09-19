<?php

namespace Sal\Seven\Tests\Adapter\Ssh;

use PHPUnit\Framework\TestCase;
use Sal\Seven\Adapter\Ssh\SshAdapter;

class SshAdapterTest extends TestCase
{
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
