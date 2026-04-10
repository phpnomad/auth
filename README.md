# phpnomad/auth

[![Latest Version](https://img.shields.io/packagist/v/phpnomad/auth.svg)](https://packagist.org/packages/phpnomad/auth) [![Total Downloads](https://img.shields.io/packagist/dt/phpnomad/auth.svg)](https://packagist.org/packages/phpnomad/auth) [![PHP Version](https://img.shields.io/packagist/php-v/phpnomad/auth.svg)](https://packagist.org/packages/phpnomad/auth) [![License](https://img.shields.io/packagist/l/phpnomad/auth.svg)](https://packagist.org/packages/phpnomad/auth)

`phpnomad/auth` is the authentication and authorization layer for [PHPNomad](https://phpnomad.com) applications. It defines the interfaces your code depends on (`JwtStrategy`, `HashStrategy`, `CurrentUserResolverStrategy`, `PasswordResetStrategy`, `SecretProvider`, and friends), a `User`/`Session`/`Action` domain model, an authorization policy evaluator, a `JwtService` with a fluent payload builder, and lifecycle events for login, logout, and permission initialization. Your application code talks to the interfaces, never to a platform's auth system directly.

Concrete implementations live outside this package. `phpnomad/firebase-jwt-integration` provides the production `JwtStrategy` using `firebase/php-jwt`. Platform integrations like `phpnomad/wordpress-integration` supply the user resolver, session, and password reset strategies for their host. Your own application fills in the gaps (a `SecretProvider` that reads from config, a custom `CurrentUserResolverStrategy` for a SaaS, and so on).

## Installation

```bash
composer require phpnomad/auth
```

## Quick Start

Authentication in PHPNomad is a set of interfaces you implement for your platform and then bind in your bootstrapper. A typical initializer maps each auth interface to a concrete class using the `HasClassDefinitions` contract from `phpnomad/loader`.

```php
<?php

namespace MyApp;

use MyApp\Auth\AppCurrentContextResolver;
use MyApp\Auth\AppCurrentUserResolver;
use MyApp\Auth\AppHashStrategy;
use MyApp\Auth\AppPasswordResetStrategy;
use MyApp\Auth\AppSecretProvider;
use PHPNomad\Auth\Interfaces\CurrentContextResolverStrategy;
use PHPNomad\Auth\Interfaces\CurrentUserResolverStrategy;
use PHPNomad\Auth\Interfaces\HashStrategy;
use PHPNomad\Auth\Interfaces\JwtStrategy;
use PHPNomad\Auth\Interfaces\PasswordResetStrategy;
use PHPNomad\Auth\Interfaces\SecretProvider;
use PHPNomad\JWT\Firebase\Integration\Strategies\FirebaseJwt;
use PHPNomad\Loader\Interfaces\HasClassDefinitions;

class AuthInitializer implements HasClassDefinitions
{
    public function getClassDefinitions(): array
    {
        return [
            AppCurrentContextResolver::class => CurrentContextResolverStrategy::class,
            AppCurrentUserResolver::class    => CurrentUserResolverStrategy::class,
            AppHashStrategy::class           => HashStrategy::class,
            AppPasswordResetStrategy::class  => PasswordResetStrategy::class,
            AppSecretProvider::class         => SecretProvider::class,
            FirebaseJwt::class               => JwtStrategy::class,
        ];
    }
}
```

With those bindings in place, application code depends on the interfaces or on `JwtService`, which wraps a `JwtStrategy` and a `SecretProvider` so callers never handle the signing key directly.

```php
<?php

use DateTime;
use PHPNomad\Auth\Builders\JwtPayloadBuilder;
use PHPNomad\Auth\Services\JwtService;

class IssueAccessToken
{
    public function __construct(protected JwtService $jwt) {}

    public function forUser(int $userId): string
    {
        $payload = (new JwtPayloadBuilder())
            ->setIssuer('my-app')
            ->setSubject((string) $userId)
            ->setIssuedAt(new DateTime('now'))
            ->setExpirationTime(new DateTime('+1 hour'))
            ->build();

        return $this->jwt->encodeJwt($payload);
    }
}
```

Decoding throws `TokenExpiredException` or `InvalidSignatureException` from `PHPNomad\Auth\Exceptions` on failure, which you can catch in middleware to return a 401.

## Key Concepts

- Strategy interfaces define the platform-facing surface. `JwtStrategy`, `HashStrategy`, `CurrentUserResolverStrategy`, `CurrentContextResolverStrategy`, `PasswordResetStrategy`, `SecretProvider`, `LoginUrlProvider`, and `PlatformContextProvider` each have one job and one implementation per platform.
- `User`, `Session`, and `Action` are the domain model. A `Session` carries the current context (`SessionContexts::Rest`, `Web`, `CommandLine`, `Admin`, etc.) and the `Action` the caller intends to perform. A `User` knows whether it can do a given `Action` via `canDoAction()`.
- Authorization runs through `AuthorizationPolicy` objects. `AuthPolicyEvaluatorService` takes a list of policies and denies on the first failing one. Built-in policies include `UserCanDoActionPolicy` (checks the user against the session's intended action) and `SessionTypePolicy` (locks an endpoint to a specific context).
- `JwtService` is the class application code usually depends on, not `JwtStrategy` directly. It wraps the strategy with a `SecretProvider` and handles `encodeJwt` and `decodeJwt`. `JwtPayloadBuilder` builds standard claims (`iss`, `sub`, `aud`, `exp`, `nbf`, `iat`, `jti`) with a fluent API.
- `UserLoggedIn`, `UserLoggedOut`, and `UserPermissionsInitialized` broadcast through `phpnomad/event` so listeners can hook in without modifying the auth flow.

## Documentation

Full documentation lives at [phpnomad.com](https://phpnomad.com), including the bootstrapping guide and the dependency injection patterns that wire strategies into your application.

## License

MIT, see [LICENSE.txt](LICENSE.txt) for the full text.
