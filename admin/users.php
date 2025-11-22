<?php
require_once "../config.php";
require_once "../functions.php";
require_login();
if (!is_admin()) {
    header("Location: index.php");
    exit;
}
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($mode === 'delete' && $id > 0 && $id != $_SESSION['user_id']) {
    $conn->query("DELETE FROM users WHERE id=" . $id);
    header("Location: users.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    $password = $_POST['password'];
    if ($mode === 'edit' && $id > 0) {
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE users SET fullname=?, username=?, role=?, password=? WHERE id=?");
            $stmt->bind_param("ssssi", $fullname, $username, $role, $hash, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET fullname=?, username=?, role=? WHERE id=?");
            $stmt->bind_param("sssi", $fullname, $username, $role, $id);
        }
        $stmt->execute();
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (fullname,username,password,role,created_at) VALUES (?,?,?,?,NOW())");
        $stmt->bind_param("ssss", $fullname, $username, $hash, $role);
        $stmt->execute();
    }
    header("Location: users.php");
    exit;
}
$editing = null;
if ($mode === 'edit' && $id > 0) {
    $res = $conn->query("SELECT * FROM users WHERE id=" . $id . " LIMIT 1");
    $editing = $res->fetch_assoc();
}
$list = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Pengguna</title>
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
            <a href="categories.php">Kategori</a>
            <a href="users.php" class="active">Pengguna</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </aside>
    <main class="admin-main">
        <h1>Manajemen Pengguna</h1>
        <div class="admin-grid">
            <div class="admin-panel">
                <h2><?php echo $editing ? "Edit Pengguna" : "Tambah Pengguna"; ?></h2>
                <form method="post">
                    <label>Nama Lengkap</label>
                    <input type="text" name="fullname" value="<?php echo $editing ? esc($editing['fullname']) : ''; ?>" required>
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo $editing ? esc($editing['username']) : ''; ?>" required>
                    <label>Role</label>
                    <select name="role">
                        <option value="admin" <?php if ($editing && $editing['role'] === 'admin') echo "selected"; ?>>Admin</option>
                        <option value="editor" <?php if ($editing && $editing['role'] === 'editor') echo "selected"; ?>>Editor</option>
                    </select>
                    <label>Password</label>
                    <input type="password" name="password" placeholder="<?php echo $editing ? "Biarkan kosong jika tidak diganti" : ""; ?>" <?php echo $editing ? "" : "required"; ?>>
                    <button type="submit"><?php echo $editing ? "Update" : "Simpan"; ?></button>
                </form>
            </div>
            <div class="admin-panel">
                <h2>Daftar Pengguna</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $list->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo esc($row['fullname']); ?></td>
                                <td><?php echo esc($row['username']); ?></td>
                                <td><?php echo esc($row['role']); ?></td>
                                <td><?php echo date('d M Y H:i', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <a href="users.php?mode=edit&id=<?php echo $row['id']; ?>">Edit</a>
                                    <?php if ($row['id'] != $_SESSION['user_id']) { ?>
                                        <a href="users.php?mode=delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Hapus pengguna ini?')">Hapus</a>
                                    <?php } ?>
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
