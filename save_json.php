<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
    exit;
}

$directory = isset($input['directory']) ? $input['directory'] : 'json';
$file      = isset($input['file'])      ? $input['file']      : '';

// Security: prevent directory traversal
$directory = str_replace(['..', './', '\\'], '', $directory);
$file      = str_replace(['..', '/', '\\'], '', $file);

$fullPath = $directory . '/' . $file;

// Validate available_methods
$validMethods = ['GET','POST','PUT','PATCH','DELETE'];
$availableMethods = [];
if (isset($input['available_methods']) && is_array($input['available_methods'])) {
    foreach ($input['available_methods'] as $m) {
        if (in_array($m, $validMethods)) $availableMethods[] = $m;
    }
}
if (empty($availableMethods)) $availableMethods = $validMethods;

// Validate body mode
$validBodyModes = ['none','json','formdata','urlencoded','raw'];
$bodyMode = isset($input['default_body_mode']) && in_array($input['default_body_mode'], $validBodyModes)
    ? $input['default_body_mode'] : 'none';

// Validate auth type
$validAuthTypes = ['none','bearer','basic','apikey'];
$authType = isset($input['default_auth_type']) && in_array($input['default_auth_type'], $validAuthTypes)
    ? $input['default_auth_type'] : 'none';

// Validate theme
$theme = isset($input['theme']) && in_array($input['theme'], ['light','dark']) ? $input['theme'] : 'dark';

// Validate method
$method = isset($input['default_method']) && in_array($input['default_method'], $validMethods)
    ? $input['default_method'] : 'GET';

// Timeout
$timeout = null;
if (isset($input['default_timeout']) && $input['default_timeout'] !== null && $input['default_timeout'] !== '') {
    $timeout = intval($input['default_timeout']);
}

// Numeric fields
$historyMax  = isset($input['history_max_items']) ? intval($input['history_max_items']) : 100;
$sidebarW    = isset($input['sidebar_width'])     ? intval($input['sidebar_width'])     : 260;

// Accent color validation
$accentColor = '#ff6c37';
if (isset($input['accent_color']) && preg_match('/^#[0-9a-fA-F]{3,6}$/', $input['accent_color'])) {
    $accentColor = $input['accent_color'];
}

// Prepare save data
$saveData = [
    'app_name'            => $input['app_name']          ?? '',
    'app_version'         => $input['app_version']       ?? '',
    'app_description'     => $input['app_description']   ?? '',
    'author'              => $input['author']             ?? '',
    'theme'               => $theme,
    'proxy_url'           => $input['proxy_url']          ?? '/proxy.php',
    'default_timeout'     => $timeout,
    'default_method'      => $method,
    'default_body_mode'   => $bodyMode,
    'default_auth_type'   => $authType,
    'available_methods'   => $availableMethods,
    'history_max_items'   => $historyMax,
    'sidebar_width'       => $sidebarW,
    'accent_color'        => $accentColor,
    'show_shortcut_hints' => isset($input['show_shortcut_hints']) ? (bool)$input['show_shortcut_hints'] : true,
];

// Create directory if not exists
if (!is_dir($directory)) {
    mkdir($directory, 0755, true);
}

if (file_put_contents($fullPath, json_encode($saveData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
    echo json_encode(['status' => 'success', 'message' => '✓ Configuration saved successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to write file. Check permissions.']);
}
?>