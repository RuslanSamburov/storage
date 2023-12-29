<?php

namespace Storage\Storage\Core;

class Response
{
    public static function api_headers(): void {
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Content-Type: application/json; charset=UTF-8');
    }
    public static function redirect(string $url, int $status = 302): void
    {
        header('Location: ' . $url, TRUE, $status);
    }
}
