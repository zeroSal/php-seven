<?php

namespace Sal\Clientify\Model;

/**
 * @author Luca Saladino <sal65535@protonmail.com>
 */
class CommandResult
{
    public function __construct(
        private readonly int $returnCode,
        private readonly string $standardOutput,
        private readonly string $errorOutput,
        private readonly string $command,
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
