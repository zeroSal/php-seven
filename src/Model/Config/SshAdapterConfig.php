<?php

namespace Sal\Seven\Model\Config;

class SshAdapterConfig
{
    /**
     * @param string[] $options
     */
    public function __construct(
        private array $options,
    ) {
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
        $this->options[] = $option;

        return $this;
    }
}
