<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

// Подключение к БД
$host   = getenv('PGHOST')     ?: '127.0.0.1';
$port   = getenv('PGPORT')     ?: 5432;
$db     = getenv('PGDATABASE') ?: 'php_site';
$user   = getenv('PGUSER')     ?: 'postgres';
$pass   = getenv('PGPASSWORD') ?: 'password';

$dsn = "pgsql:host=$host;port=$port;dbname=$db";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Ошибка подключения к базе данных',
        'details' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

switch ($action) {
    case 'hello':
        echo json_encode([
            'message' => 'Hello, API!',
            'method'  => $method
        ], JSON_UNESCAPED_UNICODE);
        break;

    case 'echo':
        $input = json_decode(file_get_contents('php://input'), true);
        echo json_encode([
            'received' => $input ?? $_GET ?? $_POST,
            'headers'  => getallheaders(),
            'method'   => $method
        ], JSON_UNESCAPED_UNICODE);
        break;

    case 'date':
        echo json_encode([
            'timestamp' => time(),
            'date'      => date('Y-m-d'),
            'time'      => date('H:i:s'),
            'tz'        => date_default_timezone_get()
        ], JSON_UNESCAPED_UNICODE);
        break;

    case 'all':
        try {
            $stmt = $pdo->query('SELECT * FROM articles ORDER BY id');
            $rows = $stmt->fetchAll();

            echo json_encode($rows, JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Ошибка выборки из БД',
                'details' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case 'get':
        $id = $_GET['id'] ?? null;

        if (!$id || !ctype_digit($id)) {
            http_response_code(400);
            echo json_encode([
                'error' => 'Нужно передать корректный параметр id'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        try {
            $stmt = $pdo->prepare('SELECT * FROM articles WHERE id = :id LIMIT 1');
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch();

            if (!$row) {
                http_response_code(404);
                echo json_encode([
                    'error' => 'Запись не найдена'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode($row, JSON_UNESCAPED_UNICODE);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Ошибка выборки из БД',
                'details' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
case 'del':
    $id = $_GET['id'] ?? null;

    if (!$id || !ctype_digit($id)) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Нужно передать корректный параметр id'
        ], JSON_UNESCAPED_UNICODE);
        break;
    }

    try {
        $stmt = $pdo->prepare('DELETE FROM articles WHERE id = :id');
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode([
                'error' => 'Запись не найдена'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Запись удалена',
                'id'      => (int)$id
            ], JSON_UNESCAPED_UNICODE);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Ошибка удаления из БД',
            'details' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    break;
    default:
        http_response_code(404);
        echo json_encode([
            'error' => 'Endpoint not found'
        ], JSON_UNESCAPED_UNICODE);
}
?>
