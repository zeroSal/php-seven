<?php

namespace Sal\Clientify\Adapter\Http;

use GuzzleHttp\Exception\GuzzleException;
use Sal\Clientify\Model\Http\Authentication\HttpAuthentication;
use Sal\Clientify\Model\Http\Header\HttpHeader;
use Sal\Clientify\Model\Http\HttpParameter;
use Sal\Clientify\Model\Http\HttpResponse;

/**
 * @author Luca Saladino <sal65535@protonmail.com>
 */
interface HttpAdapterInterface
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
}
