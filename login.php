<?php
require_once "config.php";
require_once "functions.php";
if (isset($_SESSION['user_id'])) {
    header("Location: admin/index.php");
    exit;
}
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($user = $res->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];
            header("Location: admin/index.php");
            exit;
        }
    }
    $error = "Username atau password salah.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/style.css">
<script>
function validateLogin(e) {
    var u = document.getElementById("username");
    var p = document.getElementById("password");
    if (u.value.trim() === "" || p.value.trim() === "") {
        e.preventDefault();
        alert("Username dan password wajib diisi");
    }
}
</script>
</head>
<body class="auth-body">
<div class="auth-card">
    <h1>Login Admin</h1>
    <?php if ($error !== "") { ?>
        <div class="alert"><?php echo esc($error); ?></div>
    <?php } ?>
    <form method="post" onsubmit="validateLogin(event)">
        <input id="username" type="text" name="username" placeholder="Username">
        <input id="password" type="password" name="password" placeholder="Password">
        <button type="submit">Login</button>
    </form>
    <a href="index.php">Kembali ke Beranda</a>
</div>
</body>
</html>
