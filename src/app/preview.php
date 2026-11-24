<?php
session_start();
$secure = filter_var(getenv('SECURE'), FILTER_VALIDATE_BOOLEAN);

if (!isset($_SESSION['id'])) {
    echo("no permission");
    exit;
}

$rawUrl = $_GET['url'] ?? '';

if ($secure) {
    [$host, $dest] = vet_url($rawUrl);
    $url = parse_url($rawUrl);
    $port = $url['port'] ?? (strtolower($url['scheme']) === 'https' ? 443 : 80);
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $rawUrl,
        CURLOPT_RESOLVE => ["$host:$port:$dest"],
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_RETURNTRANSFER => true,
    ]);

    $body = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    if ($body == false || $status != 200) {
        http_response_code(502);
        exit('Could not fetch image');
    }

    echo $body;
} else {
    $data = @file_get_contents($rawUrl);
    
    if ($data == false) {
        http_response_code(502);
        exit('Could not fetch image');
    }
    
    echo $data;
}

function vet_url(string $raw): array {
    $url = parse_url($raw);
    if ($url === false || empty($url['scheme']) || empty($url['host'])) {
        http_response_code(400);
        exit("Invalid URL");
    }

    if (!in_array(strtolower($url['scheme']), ['https', 'http'], true)) {
        http_response_code(400);
        exit("Invalid URL Scheme");
    }

    $dests = gethostbynamel($url['host']);
    if ($dests === false) {
        http_response_code(400);
        exit("Invalid URL resolution");
    }

    foreach ($dests as $dest) {
        $public = filter_var($dest, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        if ($public === false) {
            http_response_code(400);
            exit("Invalid URL resolution");
        }
    }

    return [$url['host'], $dests[0]];
}