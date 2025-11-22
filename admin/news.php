<?php
require_once "../config.php";
require_once "../functions.php";
require_login();
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($mode === 'delete' && $id > 0) {
    $conn->query("DELETE FROM news WHERE id=" . $id);
    header("Location: news.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category_id = (int)$_POST['category_id'];
    $content = trim($_POST['content']);
    $status = $_POST['status'];
    $slug = slugify($title);
    $published_at = $status === 'published' ? date('Y-m-d H:i:s') : null;
    $image_name = null;
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = array('jpg','jpeg','png','webp');
        if (in_array($ext, $allowed) && $_FILES['image']['size'] <= 2*1024*1024) {
            $image_name = time() . "_" . preg_replace('/[^a-zA-Z0-9\._-]/','',$_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $image_name);
        }
    }
    if ($mode === 'edit' && $id > 0) {
        $sql = "UPDATE news SET category_id=?, title=?, slug=?, content=?, status=?, updated_at=NOW()";
        if ($published_at) {
            $sql .= ", published_at=?";
        }
        if ($image_name) {
            $sql .= ", image=?";
        }
        $sql .= " WHERE id=?";
        if ($published_at && $image_name) {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssssii", $category_id, $title, $slug, $content, $status, $published_at, $image_name, $id);
        } elseif ($published_at && !$image_name) {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssssi", $category_id, $title, $slug, $content, $status, $published_at, $id);
        } elseif (!$published_at && $image_name) {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssssi", $category_id, $title, $slug, $content, $status, $image_name, $id);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issssi", $category_id, $title, $slug, $content, $status, $id);
        }
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO news (category_id,title,slug,content,image,author_id,published_at,status,created_at) VALUES (?,?,?,?,?,?,?,?,NOW())");
        $stmt->bind_param("issssiss", $category_id, $title, $slug, $content, $image_name, $_SESSION['user_id'], $published_at, $status);
        $stmt->execute();
    }
    header("Location: news.php");
    exit;
}
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$editing = null;
if ($mode === 'edit' && $id > 0) {
    $res = $conn->query("SELECT * FROM news WHERE id=" . $id . " LIMIT 1");
    $editing = $res->fetch_assoc();
}
$list = $conn->query("SELECT n.id,n.title,n.status,n.published_at,c.name AS category_name FROM news n JOIN categories c ON n.category_id=c.id ORDER BY n.created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Berita</title>
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
            <a href="news.php" class="active">Berita</a>
            <a href="categories.php">Kategori</a>
            <?php if (is_admin()) { ?>
            <a href="users.php">Pengguna</a>
            <?php } ?>
            <a href="../logout.php">Logout</a>
        </nav>
    </aside>
    <main class="admin-main">
        <h1>Manajemen Berita</h1>
        <div class="admin-grid">
            <div class="admin-panel">
                <h2><?php echo $editing ? "Edit Berita" : "Tambah Berita"; ?></h2>
                <form method="post" enctype="multipart/form-data">
                    <label>Judul</label>
                    <input type="text" name="title" value="<?php echo $editing ? esc($editing['title']) : ''; ?>" required>
                    <label>Kategori</label>
                    <select name="category_id" required>
                        <option value="">Pilih Kategori</option>
                        <?php while ($c = $categories->fetch_assoc()) { ?>
                            <option value="<?php echo $c['id']; ?>" <?php if ($editing && $editing['category_id'] == $c['id']) echo "selected"; ?>>
                                <?php echo esc($c['name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                    <label>Konten</label>
                    <textarea name="content" rows="8" required><?php echo $editing ? esc($editing['content']) : ''; ?></textarea>
                    <label>Gambar Utama</label>
                    <input type="file" name="image" accept="image/*">
                    <label>Status</label>
                    <select name="status">
                        <option value="draft" <?php if ($editing && $editing['status'] === 'draft') echo "selected"; ?>>Draft</option>
                        <option value="published" <?php if ($editing && $editing['status'] === 'published') echo "selected"; ?>>Published</option>
                    </select>
                    <button type="submit"><?php echo $editing ? "Update" : "Simpan"; ?></button>
                </form>
            </div>
            <div class="admin-panel">
                <h2>Daftar Berita</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Publish</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $list->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo esc($row['title']); ?></td>
                                <td><?php echo esc($row['category_name']); ?></td>
                                <td><?php echo esc($row['status']); ?></td>
                                <td><?php echo $row['published_at'] ? date('d M Y H:i', strtotime($row['published_at'])) : '-'; ?></td>
                                <td>
                                    <a href="news.php?mode=edit&id=<?php echo $row['id']; ?>">Edit</a>
                                    <a href="news.php?mode=delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Hapus berita ini?')">Hapus</a>
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
