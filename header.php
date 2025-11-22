<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/functions.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>News Portal</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a href="<?php echo $base_url; ?>" class="logo">News Portal</a>
        <nav class="main-nav">
            <a href="<?php echo $base_url; ?>">Beranda</a>
            <?php
            $catq = $conn->query("SELECT id,name,slug FROM categories ORDER BY name ASC");
            while ($c = $catq->fetch_assoc()) {
                echo '<a href="' . $base_url . '/category.php?slug=' . esc($c['slug']) . '">' . esc($c['name']) . '</a>';
            }
            ?>
        </nav>
        <form class="search-form" action="<?php echo $base_url; ?>/search.php" method="get">
            <input type="text" name="q" placeholder="Cari berita..." required>
            <button type="submit">Cari</button>
        </form>
    </div>
</header>
<main class="site-main container">
