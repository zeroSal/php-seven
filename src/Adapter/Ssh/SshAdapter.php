<?php

namespace Sal\Seven\Adapter\Ssh;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Sal\Seven\Model\CommandResult;
use Sal\Seven\Model\File;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

/**
 * @author Luca Saladino <sal65535@protonmail.com>
 */
class SshAdapter implements SshAdapterInterface
{
    /** @var string[] */
    private $options;

    private string $host;
    private string $user = 'root';
    private ?int $timeout = 60;

    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = new NullLogger();

        $this->options = [
            '-o', 'ControlMaster=auto',
            '-o', 'ControlPath=/tmp/php-seven-ssh-%C',
            '-o', 'ControlPersist=60m',
            '-o', 'HostKeyAlgorithms=+ssh-dss',
            '-o', 'StrictHostKeyChecking=no',
            '-o', 'UserKnownHostsFile=/dev/null',
        ];
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    public function setUser(string $user): void
    {
        $this->user = $user;
    }

    public function setTimeout(?int $timeout): void
    {
        $this->timeout = $timeout;

        $strTimeout = null !== $timeout ? \strval($timeout) : '0';
        $this->addOption('ConnectTimeout='.$strTimeout);
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getTimeout(): ?int
    {
        return $this->timeout;
    }

    /**
     * Downloads a file via SCP killing the process when $timeout seconds are reached.
     * If the timeout is null, then no timeout is set to the download process.
     * Executes: scp -o <options> user@host:$sourceFilePath $destinationFolder.
     *
     * @throws \RuntimeException
     * @throws ProcessTimedOutException
     */
    public function secureCopyFileDownload(
        string $sourceFilePath,
        string $destinationFolder = '/tmp/',
        ?int $timeout = null,
    ): void {
        $commandLine = array_merge(
            ['scp'],
            $this->options,
            ["{$this->user}@{$this->host}:$sourceFilePath", $destinationFolder]
        );

        $this->logger->debug(implode(' ', $commandLine));

        $proc = new Process($commandLine);
        $proc->setTimeout($timeout);
        $proc->run();

        if (!$proc->isSuccessful()) {
            throw new \RuntimeException("SCP failed downloading $sourceFilePath.");
        }
    }

    /**
     * Uploads a file via SCP killing the process when $timeout seconds are reached.
     * If the timeout is null, then no timeout is set to the upload process.
     * Executes: scp -o <options> $sourceFilePath user@host:$destinationFolder.
     *
     * @throws \RuntimeException
     * @throws ProcessTimedOutException
     */
    public function secureCopyFileUpload(
        string $sourceFilePath,
        string $destinationFolder = '/tmp',
        ?int $timeout = null,
    ): File {
        if ('/' === substr($destinationFolder, -1)) {
            throw new \InvalidArgumentException('The destination folder path cannot end with a slash.');
        }

        if (!file_exists($sourceFilePath)) {
            throw new RuntimeException('The source file does not exist.');
        }

        $commandLine = array_merge(
            ['scp'],
            $this->options,
            [$sourceFilePath, "{$this->user}@{$this->host}:$destinationFolder/"]
        );

        $this->logger->debug(implode(' ', $commandLine));

        $proc = new Process($commandLine);
        $proc->setTimeout($timeout);
        $proc->run();

        if (!$proc->isSuccessful()) {
            throw new \RuntimeException($proc->getErrorOutput());
        }

        $fileName = $sourceFilePath;
        if (false !== strpos($sourceFilePath, '/')) {
            $fileName = array_reverse(explode('/', $sourceFilePath))[0];
        }

        $fileSize = filesize($sourceFilePath);
        if (false === $fileSize) {
            throw new RuntimeException('Cannot retrieve the size of the file to upload.');
        }

        return new File(
            "{$destinationFolder}/{$fileName}",
            $fileSize
        );
    }

    /**
     * Runs a command via SSH providing $pipedInput in command STDIN.
     * The process will be killed when $timeout seconds are reached.
     * If the timeout is null, then no timeout is set to the process.
     * Executes: ssh -o <option> user@host $command | $pipedInput.
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
        $commandLine = array_merge(
            ['ssh'],
            $this->options,
            ["{$this->user}@$this->host"],
            [(new Process($command))->getCommandLine()]
        );

        $this->logger->debug(implode(' ', $commandLine));

        $proc = new Process($commandLine);

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
     * Wait for the SSH service initialization, trying to perform a connection.
     *
     * @throws ProcessTimedOutException
     * @throws RuntimeException
     */
    public function waitForSshLogin(): void
    {
        $commandLine = array_merge(
            ['ssh'],
            $this->options,
            ["{$this->user}@$this->host"]
        );

        $this->logger->debug(implode(' ', $commandLine));

        $process = new Process($commandLine);
        $process->setTimeout(null);
        do {
            $code = $process->run();
        } while (0 !== $code);
    }

    /**
     * @return string[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function addOption(string $option): self
    {
        $this->options = array_merge(
            $this->options,
            ['-o', $option]
        );

        return $this;
    }

    public function addConfigFile(string $path): self
    {
        $this->options = array_merge(
            $this->options,
            ['-F', $path]
        );

        return $this;
    }

    public function addJump(string $host): self
    {
        $this->options = array_merge(
            $this->options,
            ['-J', $host]
        );

        return $this;
    }

    public function addIdentityFile(string $path): self
    {
        $this->options = array_merge(
            $this->options,
            ['-i', $path]
        );

        return $this;
    }
}
