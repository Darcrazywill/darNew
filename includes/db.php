<?php
// includes/db.php
// Единая точка подключения к PostgreSQL

// 1. Загружаем переменные из .env (если файл существует)
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

// 2. Получаем параметры подключения
$host   = getenv('PGHOST')     ?: '127.0.0.1';
$port   = getenv('PGPORT')     ?: 5432;
$dbname = getenv('PGDATABASE') ?: 'php_site';
$user   = getenv('PGUSER')     ?: 'postgres';
$pass   = getenv('PGPASSWORD') ?: '';

// 3. Подключаемся
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$pass");

if (!$conn) {
    die("❌ Ошибка подключения к PostgreSQL: " . pg_last_error());
}
// Переменная $conn теперь доступна во всех файлах, где подключён db.php
?>


