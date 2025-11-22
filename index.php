<?php
include "header.php";
$headline = $conn->query("SELECT n.*, c.name AS category_name, u.fullname AS author_name FROM news n JOIN categories c ON n.category_id=c.id JOIN users u ON n.author_id=u.id WHERE n.status='published' ORDER BY n.published_at DESC LIMIT 1");
$latest = $conn->query("SELECT n.*, c.name AS category_name FROM news n JOIN categories c ON n.category_id=c.id WHERE n.status='published' ORDER BY n.published_at DESC LIMIT 6");
?>
<section class="headline-section">
<?php if ($h = $headline->fetch_assoc()) { ?>
    <div class="headline-card">
        <a href="news.php?slug=<?php echo esc($h['slug']); ?>">
            <?php if ($h['image']) { ?>
                <img src="uploads/<?php echo esc($h['image']); ?>" alt="<?php echo esc($h['title']); ?>">
            <?php } ?>
            <div class="headline-content">
                <span class="badge"><?php echo esc($h['category_name']); ?></span>
                <h1><?php echo esc($h['title']); ?></h1>
                <p><?php echo esc(excerpt($h['content'], 200)); ?></p>
                <div class="meta">
                    <span><?php echo date('d M Y H:i', strtotime($h['published_at'])); ?></span>
                    <span><?php echo esc($h['author_name']); ?></span>
                </div>
            </div>
        </a>
    </div>
<?php } ?>
</section>
<section class="list-section">
    <h2>Berita Terbaru</h2>
    <div class="grid">
        <?php while ($row = $latest->fetch_assoc()) { ?>
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
        <?php } ?>
    </div>
</section>
<?php
include "footer.php";
