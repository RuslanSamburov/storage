<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

function env(string $key, mixed $default = true): mixed
{
    return $_ENV[$key] ?? $default;
}
