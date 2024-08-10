<?php

function path(string $path): string
{
    return BASE_PATH . $path;
}

function path_app(string $app): string {
    return path('app' . DIRECTORY_SEPARATOR . $app);
}
