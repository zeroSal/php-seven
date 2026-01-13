<?php

namespace Sal\Seven\Adapter\Shell;

use Psr\Log\LoggerAwareInterface;
use Sal\Seven\Model\CommandResult;
use Symfony\Component\Process\Exception\ProcessTimedOutException;

interface ShellAdapterInterface extends LoggerAwareInterface
{
    /**
     * @param mixed[] $command
     *
     * @return CommandResult the command result
     *
     * @throws \RuntimeException
     * @throws ProcessTimedOutException
     */
    public function runCommand(
        array $command,
        ?string $pipedInput = null,
        ?int $timeout = null,
        ?\Closure $outCallback = null,
    ): CommandResult;
}
