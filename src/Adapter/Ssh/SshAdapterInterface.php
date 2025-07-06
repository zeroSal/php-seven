<?php

namespace Sal\Clientify\Adapter\Ssh;

use Sal\Clientify\Model\CommandResult;
use Sal\Clientify\Model\File;
use Symfony\Component\Process\Exception\ProcessTimedOutException;

/**
 * @author Luca Saladino <sal65535@protonmail.com>
 */
interface SshAdapterInterface
{
    public function setHost(string $host): void;

    public function setUser(string $user): void;

    public function setTimeout(?int $timeout): void;

    public function getUser(): string;

    public function getHost(): string;

    public function getTimeout(): ?int;

    public function waitForSshLogin(): void;

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
    ): CommandResult;

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
    ): File;

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
    ): void;

    /**
     * @return string[]
     */
    public function getOptions(): array;

    public function addOption(string $option): self;

    public function addConfigFile(string $path): self;

    public function addJump(string $host): self;

    public function addIdentityFile(string $path): self;
}
