<?php

namespace Solmer\Storage\Core;

use Solmer\Storage\Application\Models\Users;

class Auth
{
    public static function isUserAuth(): bool
    {
        return isset($_SESSION['current_user']) ? !empty($_SESSION['current_user']) : false;
    }

    public static function isUserActive(): bool
    {
        if (!self::isUserAuth()) {
            return false;
        }
        $users = new Users();
        $user = $users->get($_SESSION['current_user']);
        return $user['is_active'];
    }

    public static function auth(): bool
    {
        return self::isUserAuth() && self::isUserActive();
    }

    public static function authNoActive(): bool
    {
        return self::isUserAuth() && !self::isUserActive();
    }
}
