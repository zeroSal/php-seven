<?php

namespace Sal\Seven\Loader;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Sal\Seven\Model\Config\SshAdapterConfig;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

readonly class SshAdapterConfigLoader
{
    private LoggerInterface $logger;

    public function __construct(
        private string $path,
        private SerializerInterface $serializer,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function load(): ?SshAdapterConfig
    {
        if (!file_exists($this->path)) {
            $this->logger->error("The config file '{$this->path}' does not exist.");

            return null;
        }

        $yaml = file_get_contents($this->path);
        if (false === $yaml) {
            $this->logger->error("Unable to read the config file '{$this->path}'.");

            return null;
        }

        try {
            return $this->serializer->deserialize(
                $yaml,
                SshAdapterConfig::class,
                'yaml'
            );
        } catch (ExceptionInterface) {
            $this->logger->error("Unable to deserialize the config file '{$this->path}'.");

            return null;
        }
    }
}
