<?php
header('Content-Type: application/json');

$baseDir = realpath(__DIR__);

$dir = isset($_GET['dir']) ? $_GET['dir'] : 'json';
$file = isset($_GET['file']) ? $_GET['file'] : '';

// sanitize
$dir = ltrim($dir, '/\\');
$file = basename($file);

// build full path
$fullDir = realpath($baseDir . DIRECTORY_SEPARATOR . $dir);

// validasi directory
if ($fullDir === false || !str_starts_with($fullDir, $baseDir)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid directory']);
    exit;
}

$fullPath = $fullDir . DIRECTORY_SEPARATOR . $file;

// validasi file
if (file_exists($fullPath) && pathinfo($fullPath, PATHINFO_EXTENSION) === 'json') {
    echo file_get_contents($fullPath);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'File not found']);
}