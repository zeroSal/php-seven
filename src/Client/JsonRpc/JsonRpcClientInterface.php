<?php

namespace Sal\Seven\Client\JsonRpc;

use GuzzleHttp\Exception\GuzzleException;
use Sal\Seven\Model\JsonRpc\JsonRpcResponse;
use Symfony\Component\Console\Exception\ExceptionInterface;

interface JsonRpcClientInterface
{
    /**
     * @param mixed[] $params A JSON encodable array of parameters
     *
     * @throws GuzzleException
     * @throws \RuntimeException
     * @throws ExceptionInterface
     */
    public function call(string $method, array $params = []): JsonRpcResponse;

    public function setEndpoint(string $endpoint): void;

    public function setAuth(mixed $auth): void;
}
