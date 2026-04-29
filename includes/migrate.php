
<?php
// includes/migrate.php
require_once __DIR__ . '/env.php';
require_once __DIR__ . '/db.php';

$migrationsDir = __DIR__ . '/../migrations';
$files = array_diff(scandir($migrationsDir), ['.', '..']);
sort($files);

// Таблица логов
pg_query($conn, "CREATE TABLE IF NOT EXISTS migrations_log (
    id SERIAL PRIMARY KEY,
    migration_name VARCHAR(255) UNIQUE,
    applied_at TIMESTAMP DEFAULT NOW()
)");

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) !== 'sql') continue;
    
    $name = pathinfo($file, PATHINFO_FILENAME);
    
    // Уже применена?
    $check = pg_query_params($conn, 
        "SELECT 1 FROM migrations_log WHERE migration_name = $1", [$name]);
    
    if ($check && pg_num_rows($check) > 0) {
        echo "⏭️  [$name] уже применена\n";
        continue;
    }
    
    // Применяем
    $sql = file_get_contents("$migrationsDir/$file");
    if (pg_query($conn, $sql)) {
        pg_query_params($conn, 
            "INSERT INTO migrations_log (migration_name) VALUES ($1)", [$name]);
        echo "✅ [$name] применена: $file\n";
    } else {
        echo "❌ Ошибка [$name]: " . pg_last_error($conn) . "\n";
        exit(1);
    }
}

echo "🎉 Все миграции применены!\n";
#pg_close($conn);
?>


