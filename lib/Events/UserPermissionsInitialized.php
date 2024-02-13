<?php

namespace PHPNomad\Auth\Events;

use PHPNomad\Auth\Interfaces\User;
use PHPNomad\Events\Interfaces\Event;

class UserPermissionsInitialized implements Event
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public static function getId(): string
    {
        return 'user_permissions_initialized';
    }
}