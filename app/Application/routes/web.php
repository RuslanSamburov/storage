<?php

use Solmer\Storage\Application\Controllers\{Storage, Login};

use Solmer\Storage\Exceptions\Page404NotFound;

if ($requestPath == '') {
    $ctr = new Storage();
    $ctr->index();
} elseif ($requestPath == 'password') {
    $ctr = new Storage();
    $ctr->password();
} elseif ($requestPath == 'login') {
    $ctr = new Login();
    $ctr->login();
} elseif ($requestPath == 'logout') {
    $ctr = new Login();
    $ctr->logout();
} elseif ($requestPath == 'register') {
    $ctr = new Login();
    $ctr->register();
} elseif ($requestPath == 'activation') {
    $ctr = new Login();
    $ctr->activation();
} elseif (preg_match('/activation\/(\d+)\/(.*)/', $requestPath, $result)) {
    $ctr = new Login();
    $ctr->activationUser($result[1], $result[2]);
} elseif (preg_match('/download\/(.*)/', $requestPath, $result)) {
    $ctr = new Storage();
    $ctr->download($result[1]);
} else {
    throw new Page404NotFound();
}
