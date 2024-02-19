<?php

namespace PHPNomad\Auth\Traits;

use PHPNomad\Auth\Interfaces\CurrentContextResolverStrategy;
use PHPNomad\Auth\Models\Action;
use PHPNomad\Rest\Middleware\AuthorizationMiddleware;

trait CanCreateAuthMiddlewareFromCurrentContext
{
    protected CurrentContextResolverStrategy $currentContextResolver;

    /**
     * @param string $action
     * @param string $targetType
     * @return AuthorizationMiddleware
     */
    private function createAuthMiddlewareFromCurrentContext(string $action, string $targetType): AuthorizationMiddleware
    {
        return new AuthorizationMiddleware(
            $this->currentContextResolver->getCurrentContext(),
            new Action($action, $targetType),
        );
    }
}