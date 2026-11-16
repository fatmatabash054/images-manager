<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$id      = (int)($_GET['id'] ?? 0);

// Only the owner can load the image
$stmt = mysqli_prepare($conn, "SELECT * FROM images WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
mysqli_stmt_execute($stmt);
$image = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$image) {
    http_response_code(404);
    die("Image not found.");
}

$error = "";

if (isset($_POST['update'])) {
    csrf_check();
    $title = trim($_POST['title'] ?? '');

    if ($title === '' || mb_strlen($title) > 100) {
        $error = "Name must be between 1 and 100 characters.";
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE images SET title = ? WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, "sii", $title, $id, $user_id);
        mysqli_stmt_execute($stmt);
        header("Location: dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Image</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="card" style="margin:0 auto;">
    <h2>Edit Image</h2>

    <img src="uploads/<?php echo e($image['image_name']); ?>" width="100%" alt="">

    <?php if ($error !== ""): ?>
    <div class="alert error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
        <input type="text" name="title" value="<?php echo e($image['title']); ?>" maxlength="100" required>
        <button name="update">Save Changes</button>
    </form>
</div>

</body>
</html>
