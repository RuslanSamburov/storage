<?php

namespace Solmer\Storage\Core;

class Response
{
    public static function redirect(string $url, int $status = 302): void
    {
        header('Location: ' . $url, TRUE, $status);
    }
}
