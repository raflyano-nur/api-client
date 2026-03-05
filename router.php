<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = __DIR__ . $uri;

// jika file atau folder asli ada → langsung tampilkan
if ($uri !== '/' && file_exists($path)) {
    return false;
}

// jika request root
if ($uri === '/') {
    require __DIR__ . '/index.php';
    return;
}

// coba cari file .php
$file = __DIR__ . $uri . '.php';

if (file_exists($file)) {
    require $file;
    return;
}

// jika tidak ada
http_response_code(404);
echo "404 Not Found";