[![PHP Pipeline](https://github.com/zeroSal/php-clientify/actions/workflows/actions.yaml/badge.svg?branch=main)](https://github.com/zeroSal/php-clientify/actions/workflows/actions.yaml)

# php-clientify
PHP common client adapters library.

![lighting](https://github.com/user-attachments/assets/e06eeae2-55f9-4abb-9a9b-3dac22c68d8f)

[Icon by Freepik - Flaticon](https://www.flaticon.com/free-icons/builder)

## Why this library?

This library speeds up developing of clients via common communication protocols such as `SSH` and `HTTP`, providing `ready-to-use` and `dependency-injection-friendly` adapters.

## Installation
```bash
composer require sal/php-clientify
```

## Usage
```php
class MyAwesomeClient
{
    public function __construct() {
        // Require the instance to dependency injection
        private HttpAdapterInterface $http;
    } (
        // Set a custom timeout to HTTP requests
        $this->http->setTimeout(20);
        // Avoid TLS certificate validation
        $this->http->setVerify(false);
        // Add custom headers
        $this->http->addHeader(HttpHeaderFactory::accept(ContentType::JSON));
    );

    // Login (Bearer)
    public function login(string $server, string $username, string $password): void
    {
        // Set the base URI
        $this->http->setBaseUri("https://{$server}");

        // Perform HTTP post
        $response = $this->http->post(
            '/login', [
                // Add Form HTTP parameters
                new HttpParameter('username', $username),
                new HttpParameter('password', $password),
            ]
        );

        if (401 === $response->getStatusCode()) {
            throw new RuntimeException('Invalid credentials.');
        }

        // Retrieve the Bearer token from the response
        $body = json_decode($response->getBody() ?? '', true);
        if (!isset($body['token'])) {
            throw new RuntimeException('The server has not started the session.');
        }

        $token = $body['token'];
        // Set the token to further uses
        $this->http->setAuthorization(new HttpBearerAuthentication($token));
    }

    // Logout the adapter
    public function logout(): void
    {
        $this->http->setAuthorization(null);
    }

    // Use the logged in adapter
    public function addUser(string $email): void
    {
        $response = $this->http->post(
            '/add-user', [
                new HttpParameter('email', $email),
            ]
        );

        // Use the isSuccessful() method to check if the status code is < 400.
        if (!$response->isSuccessful()) {
            throw new RuntimeException("Response code: {$response->getStatusCode()}");
        }
    }
}
```
