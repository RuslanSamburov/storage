<?php

namespace Storage\Storage\Core;

use Storage\Storage\Application\Settings;

class Password
{
    public static function passwordHash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function checkLength(string $password): bool
    {
        return mb_strlen($password) >= Settings::LENGTH_PASSWORD;
    }

    public static function checkPasswords(string $password, string $password2): bool
    {
        return $password == $password2;
    }
}
