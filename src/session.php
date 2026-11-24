<?php
$secure = filter_var(getenv('SECURE'), FILTER_VALIDATE_BOOLEAN);

if ($secure) {
    session_set_cookie_params([
        'samesite' => 'Strict',
        'httponly' => true,
        'path' => '/'
    ]);
    ini_set('session.use_strict_mode', '1');
}
session_start();

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}