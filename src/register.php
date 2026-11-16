<?php
session_start();
include "config.php";

$error = "";

if (isset($_POST['register'])) {
    csrf_check();

    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid name and email.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hash);

        if (mysqli_stmt_execute($stmt)) {
            session_regenerate_id(true);
            $_SESSION['user_id']    = mysqli_insert_id($conn);
            $_SESSION['user_name']  = $name;
            $_SESSION['user_email'] = $email;
            header("Location: dashboard.php");
            exit;
        } elseif (mysqli_stmt_errno($stmt) == 1062) {
            $error = "This email is already registered.";
        } else {
            $error = "Could not create the account.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Register</title>
<link rel="stylesheet" href="style.css">
</head>

<body class="register-page">

<div class="card">

<h2>Create an account</h2>

<?php if ($error !== ""): ?>
<div class="alert error"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="POST">
<input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
<input type="text" name="name" placeholder="Name" required>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button name="register">Create account</button>
</form>

<a href="login.php">Already have an account? Sign in</a>

</div>

</body>
</html>
