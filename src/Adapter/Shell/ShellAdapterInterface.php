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
     * @throws \RuntimeException
     * @throws ProcessTimedOutException
     */
    public function runCommand(
        array $command,
        ?string $pipedInput = null,
        ?int $timeout = null,
        ?\Closure $outCallback = null,
    ): CommandResult;

    /**
     * @param mixed[] $env
     *
     * @throws ProcessTimedOutException
     * @throws \RuntimeException
     * @throws \LogicException
     */
    public function runBufferedCommand(
        string $commandline,
        string $outputFile,
        array $env = [],
        ?int $timeout = null,
        bool $tty = false,
        bool $pty = false,
    ): CommandResult;
}
