<?php

namespace Sal\Seven\Tests\Model\Config;

use PHPUnit\Framework\TestCase;
use Sal\Seven\Model\Config\SshAdapterConfig;

class SshAdapterConfigTest extends TestCase
{
    public function testConstructorAndGetOptions(): void
    {
        $config = new SshAdapterConfig(['a', 'b']);

        $this->assertSame(['a', 'b'], $config->getOptions());
    }

    public function testAddOption(): void
    {
        $config = new SshAdapterConfig(['x']);

        $returned = $config->addOption('y');

        $this->assertSame($config, $returned);
        $this->assertSame(['x', 'y'], $config->getOptions());
    }
}
