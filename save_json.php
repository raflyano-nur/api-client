<?php

$data = json_decode(file_get_contents("php://input"), true);

$file = basename($data['file']);

unset($data['file']);

$path = "json/" . $file;

file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));

echo json_encode([
    "status" => "success",
    "message" => "JSON berhasil disimpan"
]);