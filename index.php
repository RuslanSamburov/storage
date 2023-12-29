<?php

use Storage\Storage\Application\Controllers\Error;
use Storage\Storage\Exceptions\Page404NotFound;

require_once 'vendor/autoload.php';

$basePath = __DIR__ . DIRECTORY_SEPARATOR;

function exception_handler(object $e): void
{
    $ctr = new Error();
    if ($e instanceof Page404NotFound) {
        $ctr->page404($e);
    } else {
        $ctr->page500();
    }
}

set_exception_handler('exception_handler');

require_once './src/Application/routes/index.php';
