<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id   = (int)$_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$error     = "";

const MAX_SIZE = 5 * 1024 * 1024; // 5 MB
const ALLOWED  = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp',
];

if (isset($_POST['upload'])) {
    csrf_check();

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error = "Please choose an image.";
    } elseif ($_FILES['image']['size'] > MAX_SIZE) {
        $error = "File is too large (max 5 MB).";
    } else {
        $tmp = $_FILES['image']['tmp_name'];

        // Detect the real type from the file content, not from the browser
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $type  = $finfo->file($tmp);

        if (!isset(ALLOWED[$type]) || getimagesize($tmp) === false) {
            $error = "Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.";
        } else {
            $stored = bin2hex(random_bytes(16)) . "." . ALLOWED[$type];
            $size   = (int)$_FILES['image']['size'];
            $title  = mb_substr(pathinfo($_FILES['image']['name'], PATHINFO_FILENAME), 0, 100);

            if (move_uploaded_file($tmp, "uploads/" . $stored)) {
                $stmt = mysqli_prepare($conn,
                    "INSERT INTO images (user_id, image_name, title, image_size, image_type) VALUES (?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "issis", $user_id, $stored, $title, $size, $type);

                if (mysqli_stmt_execute($stmt)) {
                    header("Location: dashboard.php");
                    exit;
                }
                unlink("uploads/" . $stored);
                $error = "Database error while saving image.";
            } else {
                $error = "Failed to upload image.";
            }
        }
    }
}

$stmt = mysqli_prepare($conn, "SELECT * FROM images WHERE user_id = ? ORDER BY id DESC");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="top-bar">
    <h2>Images Manager</h2>
    <span class="user-email"><?php echo e($user_name); ?></span>
    <a class="logout" href="logout.php">Logout</a>
</div>

<div class="upload-container">
    <div class="card">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
            <input type="file" name="image" accept="image/*" required>
            <button type="submit" name="upload">Upload</button>
        </form>
        <?php if ($error !== "") { ?>
            <p style="color:red;"><?php echo e($error); ?></p>
        <?php } ?>
    </div>
</div>

<div class="images">
<?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <div class="image-card">
        <img src="uploads/<?php echo e($row['image_name']); ?>" alt="<?php echo e($row['title']); ?>">

        <div class="image-info">
            <h3><?php echo e($row['title']); ?></h3>

            <div class="image-meta">
                <span><?php echo e($row['image_type']); ?></span>
                <span><?php echo round($row['image_size'] / 1024 / 1024, 2); ?> MB</span>
            </div>

            <div class="actions">
                <a class="edit-btn" href="edit.php?id=<?php echo (int)$row['id']; ?>">Edit</a>
                <form method="POST" action="delete.php"
                      onsubmit="return confirm('Are you sure you want to delete this image?')">
                    <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                    <button class="delete-btn">Delete</button>
                </form>
            </div>
        </div>
    </div>
<?php } ?>
</div>

</body>
</html>
