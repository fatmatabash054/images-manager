<?php
session_start();
include "config.php";

$error = "";

if (isset($_POST['login'])) {
    csrf_check();

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        header("Location: dashboard.php");
        exit;
    }
    $error = "Invalid email or password.";
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="stylesheet" href="style.css">
</head>

<body class="login-page">

<div class="card">

<h2>Welcome back</h2>

<?php if ($error !== ""): ?>
<div class="alert error"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="POST">
<input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button name="login">Sign in</button>
</form>

<a href="register.php">Sign up</a>

</div>

</body>
</html>
