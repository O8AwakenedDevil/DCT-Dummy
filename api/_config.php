<?php
// Shared database connection + helpers for all API endpoints
// This file should never be requested directly by a browser.

declare(strict_types=1);

// Load .env file (simple parser, no external library)
function loadEnv(string $path): array {
    $env = [];
    if (!is_readable($path)) {
        return $env;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (!str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $env[trim($key)] = trim($value);
    }
    return $env;
}

$env = loadEnv(__DIR__ . '/../.env');

$host = $env['DB_HOST'] ?? '127.0.0.1';
$name = $env['DB_NAME'] ?? '';
$user = $env['DB_USER'] ?? '';
$pass = $env['DB_PASS'] ?? '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$name};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    // Don't leak DB error details to clients
    echo json_encode(['error' => 'Database unavailable']);
    error_log('DB connection failed: ' . $e->getMessage());
    exit;
}

// Standard JSON response headers used by all endpoints
function sendJson($data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: public, max-age=60'); // light caching, 1 minute
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
