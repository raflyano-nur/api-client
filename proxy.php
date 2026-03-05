<?php
/**
 * API Proxy - Handles actual HTTP requests to avoid CORS issues
 */

// Buffer output agar PHP error tidak merusak JSON response
ob_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: *');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    ob_end_clean();
    http_response_code(200);
    exit();
}

// Tangkap semua error PHP supaya tidak merusak JSON output
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'error'   => "PHP Error [$errno]: $errstr in $errfile on line $errline",
        'status'  => 0,
        'time'    => 0,
        'body'    => '',
        'headers' => [],
    ]);
    exit();
});

set_exception_handler(function($e) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'error'   => 'Exception: ' . $e->getMessage(),
        'status'  => 0,
        'time'    => 0,
        'body'    => '',
        'headers' => [],
    ]);
    exit();
});

// Cek cURL tersedia
if (!function_exists('curl_init')) {
    ob_end_clean();
    echo json_encode(['error' => 'cURL tidak tersedia di PHP. Aktifkan extension=curl di php.ini', 'status' => 0, 'time' => 0, 'body' => '', 'headers' => []]);
    exit();
}

$rawInput = file_get_contents('php://input');
$input    = json_decode($rawInput, true);

if (!$input || !isset($input['url'])) {
    ob_end_clean();
    echo json_encode(['error' => 'Invalid request - body kosong atau bukan JSON', 'status' => 0, 'time' => 0, 'body' => '', 'headers' => []]);
    exit();
}

$url      = $input['url'];
$method   = strtoupper($input['method'] ?? 'GET');
$headers  = $input['headers'] ?? [];
$body     = $input['body'] ?? null;
$bodyMode = $input['bodyMode'] ?? 'none';

// Validasi URL
if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
    ob_end_clean();
    echo json_encode(['error' => 'URL tidak valid: ' . $url, 'status' => 0, 'time' => 0, 'body' => '', 'headers' => []]);
    exit();
}

// Build cURL headers
$curlHeaders = [];
foreach ($headers as $key => $value) {
    if (trim($key) !== '' && trim($value) !== '') {
        $curlHeaders[] = "$key: $value";
    }
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,            $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT,        30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  $method);
curl_setopt($ch, CURLOPT_HEADER,         true);

if (!empty($curlHeaders)) {
    curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
}

if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE']) && $body !== null) {
    if ($bodyMode === 'formdata') {
        $postFields = is_array($body) ? $body : [];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    } elseif ($bodyMode === 'urlencoded') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(is_array($body) ? $body : []));
    } else {
        // raw JSON
        $jsonBody = is_array($body) ? json_encode($body) : (string)$body;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
    }
}

$startTime = microtime(true);
$response  = curl_exec($ch);
$endTime   = microtime(true);

$httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$curlError  = curl_error($ch);
$totalTime  = round(($endTime - $startTime) * 1000);

curl_close($ch);

// Buang output PHP yang mungkin bocor sebelum json_encode
ob_end_clean();

if ($curlError) {
    echo json_encode(['error' => $curlError, 'status' => 0, 'time' => $totalTime, 'body' => '', 'headers' => []]);
    exit();
}

if ($response === false || $response === '') {
    echo json_encode(['error' => 'Response kosong dari server target', 'status' => $httpCode ?: 0, 'time' => $totalTime, 'body' => '', 'headers' => []]);
    exit();
}

$rawHeaders   = substr($response, 0, $headerSize);
$responseBody = substr($response, $headerSize);

// Parse response headers
$parsedHeaders = [];
foreach (explode("\r\n", $rawHeaders) as $line) {
    if (strpos($line, ':') !== false) {
        [$k, $v] = explode(':', $line, 2);
        $parsedHeaders[trim($k)] = trim($v);
    }
}

// Deteksi content type
$contentType = $parsedHeaders['Content-Type'] ?? $parsedHeaders['content-type'] ?? '';
$isBinary = preg_match('/(pdf|octet-stream|image\/|zip|excel|msword)/i', $contentType);

if ($isBinary) {
    // Kirim sebagai base64 agar tidak rusak di JSON
    echo json_encode([
        'status'      => $httpCode,
        'time'        => $totalTime,
        'body'        => base64_encode($responseBody),
        'bodyType'    => 'base64',
        'contentType' => $contentType,
        'headers'     => $parsedHeaders,
        'size'        => strlen($responseBody),
    ]);
} else {
    if (json_decode($responseBody) !== null) {
      $prettyBody = json_decode($responseBody, true);
    } else {
      $prettyBody = $responseBody;
    }

    echo json_encode([
        'status'  => $httpCode,
        'time'    => $totalTime,
        'body'    => $prettyBody,
        'headers' => $parsedHeaders,
        'size'    => strlen($responseBody),
    ]);
}
