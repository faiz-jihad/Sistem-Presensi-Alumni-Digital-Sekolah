<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Tidak boleh ada echo, print, atau output apapun di sini

// Register Composer autoloader
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->handleRequest(Request::capture());