<?php

namespace Sal\Seven\Client\JsonRpc;

use GuzzleHttp\Exception\GuzzleException;
use Sal\Seven\Adapter\Http\HttpAdapterInterface;
use Sal\Seven\Factory\HttpHeaderFactory;
use Sal\Seven\Model\ContentType;
use Sal\Seven\Model\JsonRpc\JsonRpcResponse;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class JsonRpcClient implements JsonRpcClientInterface
{
    private ?string $endpoint = null;

    private mixed $auth = null;

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly HttpAdapterInterface $http,
    ) {
        $this->http->addHeader(HttpHeaderFactory::contentType(ContentType::JSON));
    }

    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    public function setAuth(mixed $auth): void
    {
        $this->auth = $auth;
    }

    /**
     * @param mixed[] $params
     *
     * @throws GuzzleException
     * @throws \RuntimeException
     * @throws ExceptionInterface
     */
    public function call(string $method, array $params = []): JsonRpcResponse
    {
        if (null === $this->endpoint) {
            throw new \RuntimeException('The JSON-RPC endpoint not set.');
        }

        $payload = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
            'id' => (string) microtime(),
            'auth' => $this->auth,
        ];

        $json = json_encode($payload);
        if (false === $json) {
            throw new \RuntimeException('Invalid JSON-RPC payload.');
        }

        $response = $this->http->post(
            $this->endpoint,
            json: $json
        );

        if (!$response->isSuccessful()) {
            throw new \RuntimeException("JSON-RPC responded with {$response->getStatusCode()}: '{$response->getBody()?->getContents()}'");
        }

        $body = $response->getBody()?->getContents();
        if (null === $body) {
            throw new \RuntimeException('No JSON-RPC response received.');
        }

        $response = $this->serializer->deserialize(
            $body,
            JsonRpcResponse::class,
            'json'
        );

        if (!$response instanceof JsonRpcResponse) {
            throw new \RuntimeException('Invalid JSON-RPC response.');
        }

        return $response;
    }
}
