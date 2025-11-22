<?php
require_once "../config.php";
require_once "../functions.php";
require_login();
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($mode === 'delete' && $id > 0) {
    $conn->query("DELETE FROM categories WHERE id=" . $id);
    header("Location: categories.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $slug = slugify($name);
    if ($mode === 'edit' && $id > 0) {
        $stmt = $conn->prepare("UPDATE categories SET name=?, slug=?, description=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $slug, $description, $id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO categories (name,slug,description,created_at) VALUES (?,?,?,NOW())");
        $stmt->bind_param("sss", $name, $slug, $description);
        $stmt->execute();
    }
    header("Location: categories.php");
    exit;
}
$editing = null;
if ($mode === 'edit' && $id > 0) {
    $res = $conn->query("SELECT * FROM categories WHERE id=" . $id . " LIMIT 1");
    $editing = $res->fetch_assoc();
}
$list = $conn->query("SELECT * FROM categories ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Kategori</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/style.css">
</head>
<body class="admin-body">
<div class="admin-layout">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>AdminPanel</h2>
        </div>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="news.php">Berita</a>
            <a href="categories.php" class="active">Kategori</a>
            <?php if (is_admin()) { ?>
            <a href="users.php">Pengguna</a>
            <?php } ?>
            <a href="../logout.php">Logout</a>
        </nav>
    </aside>
    <main class="admin-main">
        <h1>Manajemen Kategori</h1>
        <div class="admin-grid">
            <div class="admin-panel">
                <h2><?php echo $editing ? "Edit Kategori" : "Tambah Kategori"; ?></h2>
                <form method="post">
                    <label>Nama Kategori</label>
                    <input type="text" name="name" value="<?php echo $editing ? esc($editing['name']) : ''; ?>" required>
                    <label>Deskripsi</label>
                    <textarea name="description" rows="5"><?php echo $editing ? esc($editing['description']) : ''; ?></textarea>
                    <button type="submit"><?php echo $editing ? "Update" : "Simpan"; ?></button>
                </form>
            </div>
            <div class="admin-panel">
                <h2>Daftar Kategori</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Slug</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $list->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo esc($row['name']); ?></td>
                                <td><?php echo esc($row['slug']); ?></td>
                                <td>
                                    <a href="categories.php?mode=edit&id=<?php echo $row['id']; ?>">Edit</a>
                                    <a href="categories.php?mode=delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Hapus kategori?')">Hapus</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
