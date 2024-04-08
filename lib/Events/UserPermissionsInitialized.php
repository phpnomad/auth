<?php

namespace PHPNomad\Auth\Events;

use PHPNomad\Auth\Interfaces\User;
use PHPNomad\Auth\Traits\WithUser;
use PHPNomad\Events\Interfaces\Event;

class UserPermissionsInitialized implements Event
{
    use WithUser;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public static function getId(): string
    {
        return 'user_permissions_initialized';
    }
}