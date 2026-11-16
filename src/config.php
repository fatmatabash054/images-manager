<?php
// Configuration comes from environment variables (see .env.example),
// with local-XAMPP defaults so the project still runs without Docker.
$host     = getenv('DB_HOST') ?: 'localhost';
$user     = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$db       = getenv('DB_NAME') ?: 'image_manager';

mysqli_report(MYSQLI_REPORT_OFF);
$conn = mysqli_connect($host, $user, $password, $db);

if (!$conn) {
    http_response_code(500);
    die("Database connection failed.");
}
mysqli_set_charset($conn, 'utf8mb4');

// Escape output (prevents XSS)
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// CSRF protection for every state-changing POST
function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_check() {
    $sent = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $sent)) {
        http_response_code(403);
        die("Invalid CSRF token.");
    }
}
