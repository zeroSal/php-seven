[![PHP Pipeline](https://github.com/zeroSal/php-seven/actions/workflows/actions.yaml/badge.svg?branch=main)](https://github.com/zeroSal/php-seven/actions/workflows/actions.yaml)
[![codecov](https://codecov.io/gh/zeroSal/php-seven/branch/main/graph/badge.svg)](https://codecov.io/gh/zeroSal/php-seven)

# php-seven
PHP `ISO/OSI` layer 7 protocols adapters library.

<img width="128" height="128" alt="seven" src="https://github.com/user-attachments/assets/a2655528-ed04-41e7-b4bb-87b490700888" />

[Icon by smalllikeart - Flaticon](https://www.flaticon.com/free-icons/seven)

## Why this library?
This library speeds up developing of clients via common layer 7 communication protocols, providing `ready-to-use` and `dependency-injection-friendly` adapters.

## Protocols
Below the exaustive list of supported protocols.
- `HTTP(s)` authenticated via `Basic` and `Bearer` 
- `SSH` authenticated via `IdentityFile` (asymmetric key pair)

## Installation
```bash
composer require sal/php-seven
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

# Contribution
Contributions are always welcomed (both issues opening and pull requests).
## Improve the code
Fork the project, perform your improvements and open a pull request. **Note**: pull requests **MUST** include related unit tests and related documentation if needed. All pull requests **MUST** be linked to an issue.
