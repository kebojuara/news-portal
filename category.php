<?php
include "header.php";
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$stmt = $conn->prepare("SELECT * FROM categories WHERE slug=? LIMIT 1");
$stmt->bind_param("s", $slug);
$stmt->execute();
$cat_res = $stmt->get_result();
if (!$cat = $cat_res->fetch_assoc()) {
    echo "<p>Kategori tidak ditemukan.</p>";
    include "footer.php";
    exit;
}
$stmt2 = $conn->prepare("SELECT n.*, u.fullname AS author_name FROM news n JOIN users u ON n.author_id=u.id WHERE n.category_id=? AND n.status='published' ORDER BY n.published_at DESC");
$stmt2->bind_param("i", $cat['id']);
$stmt2->execute();
$list = $stmt2->get_result();
?>
<section class="list-section">
    <h1>Kategori: <?php echo esc($cat['name']); ?></h1>
    <p><?php echo esc($cat['description']); ?></p>
    <div class="grid">
        <?php while ($row = $list->fetch_assoc()) { ?>
            <article class="card">
                <a href="news.php?slug=<?php echo esc($row['slug']); ?>">
                    <?php if ($row['image']) { ?>
                        <img src="uploads/<?php echo esc($row['image']); ?>" alt="<?php echo esc($row['title']); ?>">
                    <?php } ?>
                    <div class="card-body">
                        <h3><?php echo esc($row['title']); ?></h3>
                        <p><?php echo esc(excerpt($row['content'])); ?></p>
                        <span class="meta-small"><?php echo date('d M Y', strtotime($row['published_at'])); ?></span>
                    </div>
                </a>
            </article>
        <?php } ?>
    </div>
</section>
<?php
include "footer.php";
