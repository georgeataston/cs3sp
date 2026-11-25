<?php
$secure = filter_var(getenv('SECURE'), FILTER_VALIDATE_BOOLEAN);

if ($secure) {
    session_set_cookie_params([
        'samesite' => 'Lax',
        'httponly' => true,
        'path' => '/'
    ]);
    ini_set('session.use_strict_mode', '1');
}
session_start();

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

if ($secure) {
    header("Content-Security-Policy: default-src 'self'; img-src 'self' https: data:; style-src 'self' https://cdn.jsdelivr.net; script-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'");
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header_remove('X-Powered-By');
}