<?php

namespace Solmer\Storage\Core;

class Password
{
    public static function passwordHash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function checkLength(string $password): bool
    {
        return mb_strlen($password) >= env('LENGTH_PASSWORD');
    }

    public static function checkPasswords(string $password, string $password2): bool
    {
        return $password == $password2;
    }
}
