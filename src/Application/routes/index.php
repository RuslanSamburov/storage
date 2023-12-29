<?php

$request_path = $_GET['route'];

if ($request_path && $request_path[-1] == '/') {
    $request_path = substr($request_path, 0, strlen($request_path) - 1);
}

$result = [];

require_once 'web.php';
