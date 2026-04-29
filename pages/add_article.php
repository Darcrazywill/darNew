<?php
// pages/add_article.php
require_once '../includes/db.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $author = trim($_POST['author_name'] ?? 'Администратор');
    $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
    
    if (!$title || !$content) {
        $msg = '<div class="alert error">❌ Заголовок и содержание обязательны</div>';
    } else {
        $res = pg_query_params($conn,
            "INSERT INTO articles (title, content, author_name, rating) 
             VALUES ($1, $2, $3, $4) RETURNING id",
            [$title, $content, $author, $rating]
        );
        if ($res) {
            $id = pg_fetch_result($res, 0, 0);
            header("Location: /articles.php?id=$id");
            exit;
        } else {
            $msg = '<div class="alert error">❌ ' . pg_last_error($conn) . '</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Новая статья</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="container">
        <h1>➕ Новая статья</h1>
        <?= $msg ?>
        <form method="post" class="form">
            <label>Заголовок *<br>
                <input type="text" name="title" required 
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
            </label>
            <label>Автор<br>
                <input type="text" name="author_name" 
                       value="<?= htmlspecialchars($_POST['author_name'] ?? 'Администратор') ?>">
            </label>
            <label>Рейтинг (1-5)<br>
                <select name="rating">
                    <?php for($i=1;$i<=5;$i++): ?>
                        <option value="<?= $i ?>" <?= (($_POST['rating']??5)==$i)?'selected':'' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </label>
            <label>Содержание *<br>
                <textarea name="content" required rows="10"><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
            </label>
            <button type="submit">💾 Опубликовать</button>
            <a href="/articles.php" class="btn-secondary">Отмена</a>
        </form>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>


