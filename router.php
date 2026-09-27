<?php
declare(strict_types=1);

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$uri = rawurldecode($uri);

if (str_contains($uri, '..') || preg_match('#^/(storage|includes|tools)(/|$)#i', $uri)) {
    http_response_code(404);
    echo 'Not found';
    return true;
}

if (preg_match('#^/(config\.php|bootstrap\.php|router\.php|schema\.sql)$#i', $uri)) {
    http_response_code(404);
    echo 'Not found';
    return true;
}

if (preg_match('#^/surprise/([A-Za-z0-9]{4,12})/?$#', $uri, $m)) {
    $_GET['code'] = $m[1];
    require __DIR__ . '/surprise.php';
    return true;
}

if ($uri === '/admin' || $uri === '/admin/') {
    require __DIR__ . '/admin/index.php';
    return true;
}

$file = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, $uri);
if ($uri !== '/' && is_file($file)) {
    if (PHP_SAPI === 'cli-server') {
        return false;
    }
    $types = [
        'css' => 'text/css; charset=UTF-8',
        'js' => 'text/javascript; charset=UTF-8',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'mp3' => 'audio/mpeg',
        'm4a' => 'audio/mp4',
        'wav' => 'audio/wav',
        'ogg' => 'audio/ogg',
        'ico' => 'image/x-icon',
        'woff2' => 'font/woff2',
    ];
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (isset($types[$ext])) {
        header('Content-Type: ' . $types[$ext]);
        header('X-Content-Type-Options: nosniff');
        readfile($file);
        return true;
    }
}

if ($uri === '/' || $uri === '/index.html' || $uri === '/index.php') {
    header('Content-Type: text/html; charset=UTF-8');
    readfile(__DIR__ . '/index.html');
    return true;
}

http_response_code(404);
header('Content-Type: text/html; charset=UTF-8');
echo '<!doctype html><meta charset="utf-8"><title>Lama helin</title><p style="font-family:Georgia,serif;padding:48px;color:#4a1723">Boggan lama helin.</p>';
return true;
