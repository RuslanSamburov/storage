<?php

namespace Storage\Storage\Core;

class Request
{

    public static function get(string $field): string|null
    {
        return $_GET[$field] ?? null;
    }

    public static function post(string $field): string|null
    {
        return $_POST[$field] ?? null;
    }

    public static function check_method(string $method): bool
    {
        return $_SERVER['REQUEST_METHOD'] == $method ? true : self::post('_method') == $method;
    }

    public static function is_get(): bool
    {
        return self::check_method('GET');
    }

    public static function is_post(): bool
    {
        return self::check_method('POST') && !isset($_POST['_method']);
    }

    public static function is_delete(): bool
    {
        return self::check_method('DELETE');
    }

    public static function is_put(): bool
    {
        return self::check_method('PUT');
    }
}
