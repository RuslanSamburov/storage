<?php

namespace Storage\Storage\Core;

use Storage\Storage\Application\Models\Users;

class Auth
{
    public static function is_user_auth(): bool
    {
        return isset($_SESSION['current_user']) ? !empty($_SESSION['current_user']) : false;
    }

    public static function is_user_active(): bool
    {
        if (!self::is_user_auth()) {
            return false;
        }
        $users = new Users();
        $user = $users->get($_SESSION['current_user']);
        return $user['is_active'];
    }

    public static function auth(): bool
    {
        return self::is_user_auth() && self::is_user_active();
    }

    public static function auth_no_active(): bool
    {
        return self::is_user_auth() && !self::is_user_active();
    }
}
