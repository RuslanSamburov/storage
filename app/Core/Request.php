<?php

namespace Solmer\Storage\Core;

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

    public static function checkMethod(string $method): bool
    {
        return $_SERVER['REQUEST_METHOD'] == $method ? true : self::post('_method') == $method;
    }

    public static function isGet(): bool
    {
        return self::checkMethod('GET');
    }

    public static function isPost(): bool
    {
        return self::checkMethod('POST') && !isset($_POST['_method']);
    }

    public static function isDelete(): bool
    {
        return self::checkMethod('DELETE');
    }

    public static function isPut(): bool
    {
        return self::checkMethod('PUT');
    }
}
