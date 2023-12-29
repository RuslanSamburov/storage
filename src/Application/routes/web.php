<?php

use Storage\Storage\Application\Controllers\Storage;
use Storage\Storage\Application\Controllers\Login;

use Storage\Storage\Exceptions\Page404NotFound;

if ($request_path == '') {
    $ctr = new Storage();
    $ctr->index();
} else if ($request_path == 'password') {
    $ctr = new Storage();
    $ctr->password();
} else if ($request_path == 'login') {
    $ctr = new Login();
    $ctr->login();
} else if ($request_path == 'logout') {
    $ctr = new Login();
    $ctr->logout();
} else if ($request_path == 'register') {
    $ctr = new Login();
    $ctr->register();
} else if ($request_path == 'activation') {
    $ctr = new Login();
    $ctr->activation();
} else if (preg_match('/activation\/(\d+)\/(.*)/', $request_path, $result)) {
    $ctr = new Login();
    $ctr->activationUser($result[1], $result[2]);
} else if (preg_match('/download\/(.*)/', $request_path, $result)) {
    $ctr = new Storage();
    $ctr->download($result[1]);
} else {
    throw new Page404NotFound();
}
