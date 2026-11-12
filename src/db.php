<?php
function get_db(): PDO
{
    $host   = 'db';
    $dbname = getenv('DB_NAME') ?: 'cs3sp';
    $user   = getenv('DB_USER') ?: 'student';
    $pass   = getenv('DB_PASSWORD') ?: '';

    return new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
}