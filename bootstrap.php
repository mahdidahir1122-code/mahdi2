<?php
declare(strict_types=1);

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === realpath(__FILE__)) {
    http_response_code(404);
    exit;
}

define('ROOT', __DIR__);

require ROOT . '/includes/helpers.php';

ini_set('display_errors', '0');
ini_set('log_errors', '1');
ensure_dir('storage');
ini_set('error_log', storage_path('error.log'));

if (PHP_SAPI !== 'cli') {
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_name('xanaan_owner');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => $secure,
    ]);
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

install_app();
