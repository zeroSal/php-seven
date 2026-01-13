<?php

namespace Sal\Seven\Adapter\Shell;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Sal\Seven\Model\CommandResult;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class ShellAdapter implements ShellAdapterInterface
{
    private LoggerInterface $logger;

    public function __construct(
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Runs a command on the local shell providing $pipedInput in command STDIN.
     * The process will be killed when $timeout seconds are reached.
     * If the timeout is null, then no timeout is set to the process.
     *
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
    ): CommandResult {
        $proc = new Process($command);

        $this->logger->debug($proc->getCommandLine());

        if (null !== $pipedInput) {
            $proc->setInput($pipedInput);
        }

        $proc->setTimeout($timeout);
        $code = $proc->run($outCallback);

        return new CommandResult(
            $code,
            $proc->getOutput(),
            $proc->getErrorOutput(),
            $proc->getCommandLine(),
        );
    }
}
