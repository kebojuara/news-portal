<?php
require_once "../config.php";
require_once "../functions.php";
require_login();
$news_count = $conn->query("SELECT COUNT(*) AS c FROM news")->fetch_assoc()['c'];
$cat_count = $conn->query("SELECT COUNT(*) AS c FROM categories")->fetch_assoc()['c'];
$user_count = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Admin Panel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/style.css">
</head>
<body class="admin-body">
<div class="admin-layout">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>AdminPanel</h2>
            <p><?php echo esc($_SESSION['fullname']); ?></p>
        </div>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="news.php">Berita</a>
            <a href="categories.php">Kategori</a>
            <?php if (is_admin()) { ?>
            <a href="users.php">Pengguna</a>
            <?php } ?>
            <a href="../logout.php">Logout</a>
        </nav>
    </aside>
    <main class="admin-main">
        <h1>Dashboard</h1>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Berita</h3>
                <p><?php echo $news_count; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Kategori</h3>
                <p><?php echo $cat_count; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Pengguna</h3>
                <p><?php echo $user_count; ?></p>
            </div>
        </div>
    </main>
</div>
</body>
</html>
