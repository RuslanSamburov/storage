<?php

$requestPath = $_GET['route'];

if ($requestPath && $requestPath[-1] == '/') {
    $requestPath = substr($requestPath, 0, strlen($requestPath) - 1);
}

$result = [];

require_once 'web.php';
