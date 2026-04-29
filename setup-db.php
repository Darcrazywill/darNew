<?php
// setup-db.php — запускать ОДИН раз!
echo "🚀 Инициализация БД...\n";
require_once 'includes/migrate.php';

// Тестовые данные (только для development)
if (getenv('APP_ENV') === 'development') {
    require_once 'includes/db.php';
    $check = pg_query($conn, "SELECT 1 FROM articles LIMIT 1");
    if (!$check || pg_num_rows($check) === 0) {
        pg_query_params($conn,
            "INSERT INTO articles (title, content, author_name, rating) VALUES ($1, $2, $3, $4)",
            ['Первая статья', 'Тестовый контент', 'Админ', 5]
        );
        echo "🧪 Добавлены тестовые данные (dev-режим)\n";
    }
}
echo "✅ Готово!\n";
?>


