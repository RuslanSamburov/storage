<?php

namespace Storage\Storage\Core;

use Storage\Storage\Core\Helpers;
use Storage\Storage\Application\Models\Users;

class Account
{
    public static function activationSend(string $to, int $id, string $token): bool
    {
        $values = [
            'url' => 'http://' . $_SERVER['SERVER_NAME'] . '/activation/' . $id . '/' . $token,
        ];
        return Helpers::sendMail($to, Helpers::getTxt('register_subject'), Helpers::getTxt('register_body'), $values);
    }

    public static function setUser(int $id): void
    {
        $_SESSION['current_user'] = $id;
    }

    public static function getCurrentUser(): int|bool
    {
        return $_SESSION['current_user'] ?? false;
    }

    public static function getUser(string $value, string $key_field = 'id', string $fields = '*', array $links = []): array {
        $users = new Users();
        return $users->get($value, $key_field, $fields, $links);
    }

    public static function unsetUser(): void
    {
        unset($_SESSION['current_user']);
    }

    public static function logout(): void
    {
        self::unsetUser();
        Response::redirect('/login');
    }
}
