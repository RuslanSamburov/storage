<?php

use Solmer\Storage\Application\Controllers\Error;
use Solmer\Storage\Exceptions\Page404NotFound;

const BASE_PATH = __DIR__ . DIRECTORY_SEPARATOR . '../';

require_once BASE_PATH . 'vendor/autoload.php';
require_once BASE_PATH . 'bootstrap/load.php';

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

require_once path_app('app.php');
