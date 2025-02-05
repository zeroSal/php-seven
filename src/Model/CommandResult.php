<?php

namespace Sal\Clientify\Model;

/**
 * @author Luca Saladino <sal65535@protonmail.com>
 */
class CommandResult
{
    public function __construct(
        private int $returnCode,
        private string $standardOutput,
        private string $errorOutput,
        private string $command,
    ) {
    }

    public function getReturnCode(): int
    {
        return $this->returnCode;
    }

    public function getStandardOutput(): string
    {
        return $this->standardOutput;
    }

    public function getErrorOutput(): string
    {
        return $this->errorOutput;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function isSuccess(): bool
    {
        return 0 === $this->returnCode;
    }
}
