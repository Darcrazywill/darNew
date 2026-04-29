<?php
// articles.php
require_once 'includes/db.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$res = pg_query_params($conn,
    "SELECT *, COUNT(*) OVER() as total FROM articles 
     ORDER BY created_at DESC LIMIT $1 OFFSET $2",
    [$limit, $offset]
);

$articles = []; $total = 0;
while ($row = pg_fetch_assoc($res)) {
    $articles[] = $row;
    $total = (int)$row['total'];
}
$totalPages = ceil($total / $limit);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Статьи</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main class="container">
        <div class="page-header">
            <h1>📚 Статьи</h1>
            <a href="/pages/add_article.php" class="btn-primary">➕ Добавить</a>
        </div>
        <?php if (!$articles): ?>
            <p class="empty">Статей нет. <a href="/pages/add_article.php">Создайте первую!</a></p>
        <?php else: ?>
            <div class="articles">
                <?php foreach($articles as $a): ?>
                    <article class="card">
                        <h2><?= htmlspecialchars($a['title']) ?></h2>
                        <div class="meta">
                            <span>✍️ <?= htmlspecialchars($a['author_name']) ?></span>
                            <span>⭐ <?= (int)$a['rating'] ?>/5</span>
                            <span>📅 <?= date('d.m.Y', strtotime($a['created_at'])) ?></span>
                        </div>
                        <p><?= nl2br(htmlspecialchars(mb_strimwidth($a['content'], 0, 200, '...'))) ?></p>
                        <a href="?id=<?= $a['id'] ?>">Читать →</a>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php if($totalPages > 1): ?>
            <nav class="pagination">
                <?php if($page>1): ?><a href="?page=<?= $page-1 ?>">←</a><?php endif; ?>
                <span><?= $page ?>/<?= $totalPages ?></span>
                <?php if($page<$totalPages): ?><a href="?page=<?= $page+1 ?>">→</a><?php endif; ?>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    </main>
    <?php include 'includes/footer.php'; ?>
</body>
</html>


