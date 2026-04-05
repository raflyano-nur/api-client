<?php
header('Content-Type: application/json');

$baseDir = realpath(__DIR__);

$dir = isset($_GET['dir']) ? $_GET['dir'] : 'json';

// sanitize
$dir = ltrim($dir, '/\\');

// build full path
$fullDir = realpath($baseDir . DIRECTORY_SEPARATOR . $dir);

// validasi
if ($fullDir !== false && str_starts_with($fullDir, $baseDir) && is_dir($fullDir)) {
    $files = glob($fullDir . "/*.json");

    echo json_encode([
        'status' => 'success',
        'files' => array_map('basename', $files),
        'directory' => $dir
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => "Directory '$dir' does not exist"
    ]);
}