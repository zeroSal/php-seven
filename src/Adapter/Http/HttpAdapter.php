<?php

namespace Sal\Clientify\Adapter\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Sal\Clientify\Model\Http\Authentication\HttpAuthentication;
use Sal\Clientify\Model\Http\Authentication\HttpAuthenticationType;
use Sal\Clientify\Model\Http\Authentication\HttpBasicAuthentication;
use Sal\Clientify\Model\Http\Authentication\HttpBearerAuthentication;
use Sal\Clientify\Model\Http\Header\HttpHeader;
use Sal\Clientify\Model\Http\HttpParameter;
use Sal\Clientify\Model\Http\HttpResponse;

/**
 * @author Luca Saladino <sal65535@protonmail.com>
 */
class HttpAdapter implements HttpAdapterInterface
{
    /** @var HttpHeader[] */
    private array $headers = [];
    private ?HttpAuthentication $authentication = null;
    private int $timeout = 0;
    private bool $verify = false;
    private ?string $baseUri = null;
    private bool $throwOnError = false;

    /** @var ?string[] */
    private ?array $strictResolveList = null;

    public function __construct(
        private Client $client,
    ) {
    }

    /**
     * @return mixed[]
     */
    private function buildOptions(): array
    {
        $options = [];
        $headers = [];

        if (HttpAuthenticationType::BASIC === $this->authentication?->getType()) {
            /** @var HttpBasicAuthentication $authentication */
            $authentication = $this->authentication;
            $options = array_merge(
                $options,
                [
                    'auth' => [
                        $authentication->getUsername(),
                        $authentication->getPassword(),
                    ],
                ]
            );
        }

        if (HttpAuthenticationType::BEARER === $this->authentication?->getType()) {
            /** @var HttpBearerAuthentication $authentication */
            $authentication = $this->authentication;
            $headers = array_merge(
                $headers,
                ['Authorization' => "Bearer {$authentication->getToken()}"]
            );
        }

        foreach ($this->headers as $header) {
            $headers = array_merge(
                $headers,
                [$header->getName() => $header->getValue()]
            );
        }

        $options = array_merge(
            $options,
            [
                'verify' => $this->verify,
                'timeout' => $this->timeout,
                'headers' => $headers,
                'http_errors' => $this->throwOnError,
            ]
        );

        if (null !== $this->strictResolveList) {
            $options = array_merge(
                $options,
                [
                    'curl' => [CURLOPT_RESOLVE => $this->strictResolveList],
                ]
            );
        }

        return $options;
    }

    /**
     * @throws GuzzleException
     */
    public function get(string $uri): HttpResponse
    {
        $options = $this->buildOptions();
        $uri = empty($this->baseUri) ? $uri : "{$this->baseUri}{$uri}";
        $response = $this->client->get($uri, $options);

        return new HttpResponse(
            $response->getStatusCode(),
            $response->getBody()
        );
    }

    /**
     * @param HttpParameter[] $parameters
     *
     * @throws GuzzleException
     */
    public function post(string $uri, array $parameters = [], ?string $json = null): HttpResponse
    {
        if (!empty($parameters) and null !== $json) {
            throw new \LogicException('The body must be provided as parameters or JSON. Not both.');
        }

        $uri = empty($this->baseUri) ? $uri : "{$this->baseUri}{$uri}";
        $options = [
            'body' => $json,
        ];

        if (null === $json) {
            $paramsArray = [];
            foreach ($parameters as $param) {
                $paramsArray = array_merge($paramsArray, [$param->getName() => $param->getValue()]);
            }

            $options = ['form_params' => $paramsArray];
        }

        $options = array_merge(
            $this->buildOptions(),
            $options
        );

        $response = $this->client->post($uri, $options);

        return new HttpResponse(
            $response->getStatusCode(),
            $response->getBody()
        );
    }

    /**
     * @throws GuzzleException
     */
    public function delete(string $uri): HttpResponse
    {
        $options = $this->buildOptions();
        $uri = empty($this->baseUri) ? $uri : "{$this->baseUri}{$uri}";
        $response = $this->client->delete($uri, $options);

        return new HttpResponse(
            $response->getStatusCode(),
            $response->getBody()
        );
    }

    /**
     * @param HttpParameter[] $parameters
     *
     * @throws GuzzleException
     */
    public function put(string $uri, array $parameters = [], ?string $json = null): HttpResponse
    {
        if (!empty($parameters) and null !== $json) {
            throw new \LogicException('The body must be provided as parameters or JSON. Not both.');
        }

        $uri = empty($this->baseUri) ? $uri : "{$this->baseUri}{$uri}";
        $options = [
            'body' => $json,
        ];

        if (null === $json) {
            $paramsArray = [];
            foreach ($parameters as $param) {
                $paramsArray = array_merge(
                    $paramsArray,
                    [$param->getName() => $param->getValue()]
                );
            }

            $options = ['form_params' => $paramsArray];
        }

        $options = array_merge(
            $this->buildOptions(),
            $options
        );

        $response = $this->client->put($uri, $options);

        return new HttpResponse(
            $response->getStatusCode(),
            $response->getBody()
        );
    }

    public function getBaseUri(): ?string
    {
        return $this->baseUri;
    }

    public function setBaseUri(string $baseUri): void
    {
        $this->baseUri = $baseUri;
    }

    /**
     * @return HttpHeader[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param HttpHeader[] $headers
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function addHeader(HttpHeader $header): void
    {
        $this->headers[] = $header;
    }

    public function removeHeader(HttpHeader $header): void
    {
        /** @var HttpHeader[] $headers */
        $headers = [];
        foreach ($this->headers as $h) {
            if ($h->getName() === $header->getName()
            and $h->getValue() === $header->getValue()) {
                continue;
            }

            $headers[] = $h;
        }

        $this->headers = $headers;
    }

    public function removeHeaderByName(string $name): void
    {
        /** @var HttpHeader[] $headers */
        $headers = [];
        foreach ($this->headers as $h) {
            if ($h->getName() === $name) {
                continue;
            }

            $headers[] = $h;
        }

        $this->headers = $headers;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

    public function isVerify(): bool
    {
        return $this->verify;
    }

    public function setVerify(bool $verify): void
    {
        $this->verify = $verify;
    }

    public function isAuthorized(): bool
    {
        return null !== $this->authentication;
    }

    public function getAuthorization(): ?HttpAuthentication
    {
        return $this->authentication;
    }

    public function setAuthorization(?HttpAuthentication $authentication): void
    {
        $this->authentication = $authentication;
    }

    public function doesThrowOnError(): bool
    {
        return $this->throwOnError;
    }

    public function setThrowOnError(bool $throwOnError): void
    {
        $this->throwOnError = $throwOnError;
    }

    /**
     * @param string[] $list A list of CURL strict resolving maps in <host>:<port>:<ip> format (e.g. 'me.hostname.tld:443:192.168.100.1)
     */
    public function setStrictResolveList(array $list): void
    {
        $this->strictResolveList = $list;
    }

    /**
     * @return ?string[] The list of CURL strict resolving maps in <host>:<port>:<ip> format (e.g. 'me.hostname.tld:443:192.168.100.1)
     */
    public function getStrictResolveList(): ?array
    {
        return $this->strictResolveList;
    }

    /**
     * Forces CURL to resolve the host provided to the IP given when connecting to the port set.
     */
    public function addStrictResolve(string $host, int $port, string $ip): void
    {
        $this->strictResolveList[] = "{$host}:{$port}:{$ip}";
    }
}
