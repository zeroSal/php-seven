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

    /**
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws ProcessTimedOutException
     */
    public function runBufferedCommand(
        string $commandline,
        string $outputFile,
        array $env = [],
        ?int $timeout = null,
        bool $tty = false,
        bool $pty = false,
    ): CommandResult {
        if ($tty && $pty) {
            throw new \LogicException('TTY and PTY cannot be enabled together.');
        }

        $process = Process::fromShellCommandline($commandline);
        $process->setTty($tty);
        $process->setPty($pty);
        $process->setTimeout($timeout);

        $this->logger->debug($commandline, $env);

        $process->start(null, $env);
        $process->wait();

        $output = '';
        if (is_file($outputFile)) {
            $output = (string) file_get_contents($outputFile);
            @unlink($outputFile);
        }

        $exitCode = $process->getExitCode();
        if (null === $exitCode) {
            $exitCode = $process->isSuccessful() ? 0 : 1;
        }

        return new CommandResult(
            $exitCode,
            $output,
            $process->getErrorOutput(),
            $process->getCommandLine(),
        );
    }
}
