<?php
include "header.php";
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$stmt = $conn->prepare("SELECT n.*, c.name AS category_name, u.fullname AS author_name FROM news n JOIN categories c ON n.category_id=c.id JOIN users u ON n.author_id=u.id WHERE n.slug=? AND n.status='published' LIMIT 1");
$stmt->bind_param("s", $slug);
$stmt->execute();
$res = $stmt->get_result();
if (!$news = $res->fetch_assoc()) {
    echo "<p>Berita tidak ditemukan.</p>";
    include "footer.php";
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $comment = trim($_POST['comment']);
    if ($name !== '' && $comment !== '') {
        $s = $conn->prepare("INSERT INTO comments (news_id,name,email,comment) VALUES (?,?,?,?)");
        $s->bind_param("isss", $news['id'], $name, $email, $comment);
        $s->execute();
    }
}
$comments = $conn->prepare("SELECT * FROM comments WHERE news_id=? ORDER BY created_at DESC");
$comments->bind_param("i", $news['id']);
$comments->execute();
$comments_res = $comments->get_result();
?>
<article class="detail-article">
    <h1><?php echo esc($news['title']); ?></h1>
    <div class="detail-meta">
        <span><?php echo esc($news['category_name']); ?></span>
        <span><?php echo date('d M Y H:i', strtotime($news['published_at'])); ?></span>
        <span><?php echo esc($news['author_name']); ?></span>
    </div>
    <?php if ($news['image']) { ?>
        <img class="detail-image" src="uploads/<?php echo esc($news['image']); ?>" alt="<?php echo esc($news['title']); ?>">
    <?php } ?>
    <div class="detail-content">
        <?php echo nl2br(esc($news['content'])); ?>
    </div>
</article>
<section class="comments-section">
    <h2>Komentar</h2>
    <form method="post" class="comment-form">
        <input type="text" name="name" placeholder="Nama" required>
        <input type="email" name="email" placeholder="Email (opsional)">
        <textarea name="comment" rows="4" placeholder="Komentar" required></textarea>
        <button type="submit">Kirim</button>
    </form>
    <div class="comment-list">
        <?php while ($c = $comments_res->fetch_assoc()) { ?>
            <div class="comment-item">
                <strong><?php echo esc($c['name']); ?></strong>
                <span class="meta-small"><?php echo date('d M Y H:i', strtotime($c['created_at'])); ?></span>
                <p><?php echo nl2br(esc($c['comment'])); ?></p>
            </div>
        <?php } ?>
    </div>
</section>
<?php
include "footer.php";
