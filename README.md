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

## Configuration
Adapters can be configured via a `yaml` file.

### Example (Symfony)

First of all, edit the `service.yaml` file as follows:
```yaml
# services.yaml
services:
    ssh_adapter_config_loader:
        class: Sal\Seven\Loader\SshAdapterConfigLoader
        arguments:
            $path: '%kernel.project_dir%/config/php_seven/ssh.yaml'

    ssh_adapter:
        class: Sal\Seven\Adapter\Ssh\SshAdapter
        arguments:
            $configLoader: "@ssh_adapter_config_loader"
```

Then, create the `php_seven/ssh.yaml` file as follows:
```yaml
# ssh.yaml
options:
    # - "ControlMaster=auto"
    # - "ControlPath=/tmp/php-seven-ssh-%C"
    # - "StrictHostKeyChecking=no"
    # - "ControlPersist=15m"
    # - "UserKnownHostsFile=/dev/null"
```

## Usage
Below some examples of usage.

### HTTP Client
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

### SSH Client
```php
class MyAwesomeClient
{
    public function __construct() {
        // Require the instance to dependency injection
        private SshAdapterInterface $ssh;

        // Require the instance of the logger to dependency injection (optional)
        private LoggerInterface $logger
    } (
        // Set a custom timeout to SSH connections
        $this->ssh->setTimeout(20);

        // Set the host
        $this->ssh->setHost('127.0.0.1');

        // Set the user
        $this->ssh->setUser('root');

        // Set the logger (optional)
        $this->ssh->setLogger($this->logger);

        // Add an identity file
        $this->addIdentityFile('~/.ssh/id_rsa');
    );

    public function shutdown(): void
    {
        try {
            // Run a command, saving the result in a variable
            $result = $this->ssh->runCommand(['shutdown', '-h', 'now']);
        } catch (ProcessTimedOutException $e) {
            // Catch the timeout exception
            $this->logger->error($e->getMessage());
        } catch (RuntimeException $e) {
            // Catch other exceptions
            $this->logger->error($e->getMessage());
        }

        // Use the isSuccessful() method to check if the status code is 0
        if (!$result->isSuccessful()) {
            $this->logger->error($result->getReturnCode());
            $this->logger->error($result->getOutput());

            return;
        }

        $this->logger->info($result->getReturnCode());
        $this->logger->info($result->getOutput());
    }
}
```

# Contribution
Contributions are always welcomed (both issues opening and pull requests).
## Improve the code
Fork the project, perform your improvements and open a pull request. **Note**: pull requests **MUST** include related unit tests and related documentation if needed. All pull requests **MUST** be linked to an issue.
