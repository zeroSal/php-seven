<?php

namespace Sal\Seven\Adapter\Http;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerAwareInterface;
use Sal\Seven\Model\Http\Authentication\HttpAuthentication;
use Sal\Seven\Model\Http\Header\HttpHeader;
use Sal\Seven\Model\Http\HttpParameter;
use Sal\Seven\Model\Http\HttpResponse;

/**
 * @author Luca Saladino <sal65535@protonmail.com>
 */
interface HttpAdapterInterface extends LoggerAwareInterface
{
    /**
     * @throws GuzzleException
     */
    public function get(string $uri): HttpResponse;

    /**
     * @param HttpParameter[] $parameters
     *
     * @throws GuzzleException
     */
    public function post(string $uri, array $parameters = [], ?string $json = null): HttpResponse;

    /**
     * @throws GuzzleException
     */
    public function delete(string $uri): HttpResponse;

    /**
     * @param HttpParameter[] $parameters
     *
     * @throws GuzzleException
     */
    public function put(string $uri, array $parameters = [], ?string $json = null): HttpResponse;

    /**
     * @param HttpParameter[] $parameters
     *
     * @throws GuzzleException
     */
    public function patch(string $uri, array $parameters = [], ?string $json = null): HttpResponse;

    public function getBaseUri(): ?string;

    public function setBaseUri(string $baseUri): void;

    /**
     * @return HttpHeader[]
     */
    public function getHeaders(): array;

    /**
     * @param HttpHeader[] $headers
     */
    public function setHeaders(array $headers): void;

    public function addHeader(HttpHeader $header): void;

    public function removeHeader(HttpHeader $header): void;

    public function removeHeaderByName(string $name): void;

    public function getTimeout(): int;

    public function setTimeout(int $timeout): void;

    public function isVerify(): bool;

    public function setVerify(bool $verify): void;

    public function isAuthorized(): bool;

    public function getAuthorization(): ?HttpAuthentication;

    public function setAuthorization(?HttpAuthentication $authentication): void;

    public function doesThrowOnError(): bool;

    public function setThrowOnError(bool $throwOnError): void;

    /**
     * @param string[] $list A list of CURL strict resolving maps in <host>:<port>:<ip> format (e.g. 'me.hostname.tld:443:192.168.100.1)
     */
    public function setStrictResolveList(array $list): void;

    /**
     * @return ?string[] The list of CURL strict resolving maps in <host>:<port>:<ip> format (e.g. 'me.hostname.tld:443:192.168.100.1)
     */
    public function getStrictResolveList(): ?array;

    /**
     * Forces CURL to resolve the host provided to the IP given when connecting to the port set.
     */
    public function addStrictResolve(string $host, int $port, string $ip): void;
}
