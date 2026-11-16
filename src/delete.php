<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Deleting must be a POST with a CSRF token (not a plain link)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}
csrf_check();

$user_id = (int)$_SESSION['user_id'];
$id      = (int)($_POST['id'] ?? 0);

$stmt = mysqli_prepare($conn, "SELECT image_name FROM images WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if ($row) {
    $path = "uploads/" . basename($row['image_name']);
    if (file_exists($path)) {
        unlink($path);
    }
    $stmt = mysqli_prepare($conn, "DELETE FROM images WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
    mysqli_stmt_execute($stmt);
}

header("Location: dashboard.php");
exit;
