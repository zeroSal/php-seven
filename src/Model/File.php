<?php

namespace Sal\Clientify\Model;

/**
 * @author Luca Saladino <sal65535@protonmail.com>
 */
class File
{
    public function __construct(
        private string $path,
        private ?int $size = null,
    ) {
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): void
    {
        $this->size = $size;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }
}
