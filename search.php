<?php
include "header.php";
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$result = null;
if ($q !== '') {
    $like = "%" . $q . "%";
    $stmt = $conn->prepare("SELECT n.*, c.name AS category_name FROM news n JOIN categories c ON n.category_id=c.id WHERE n.status='published' AND (n.title LIKE ? OR n.content LIKE ?) ORDER BY n.published_at DESC");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>
<section class="list-section">
    <h1>Hasil Pencarian</h1>
    <p>Kata kunci: <?php echo esc($q); ?></p>
    <div class="grid">
        <?php
        if ($result) {
            if ($result->num_rows === 0) {
                echo "<p>Tidak ada berita ditemukan.</p>";
            } else {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <article class="card">
                        <a href="news.php?slug=<?php echo esc($row['slug']); ?>">
                            <?php if ($row['image']) { ?>
                                <img src="uploads/<?php echo esc($row['image']); ?>" alt="<?php echo esc($row['title']); ?>">
                            <?php } ?>
                            <div class="card-body">
                                <span class="badge"><?php echo esc($row['category_name']); ?></span>
                                <h3><?php echo esc($row['title']); ?></h3>
                                <p><?php echo esc(excerpt($row['content'])); ?></p>
                                <span class="meta-small"><?php echo date('d M Y', strtotime($row['published_at'])); ?></span>
                            </div>
                        </a>
                    </article>
                    <?php
                }
            }
        }
        ?>
    </div>
</section>
<?php
include "footer.php";
