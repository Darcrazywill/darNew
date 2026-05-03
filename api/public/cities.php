<?php

header('Content-Type: application/json; charset=utf-8');

$country = $_GET['country'] ?? null;

if (!$country) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Нужно передать параметр country, например: ?country=Russia',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$host   = '127.0.0.1';
$port   = 5432;
$db     = 'php_site';
$user   = 'postgres';
$pass   = 'password';

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
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$sqlCountry = 'SELECT id FROM countries WHERE name = :name LIMIT 1';
$stmt = $pdo->prepare($sqlCountry);
$stmt->execute(['name' => $country]);
$countryRow = $stmt->fetch();

if (!$countryRow) {
    http_response_code(404);
    echo json_encode([
        'error' => 'Страна не найдена',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$countryId = (int)$countryRow['id'];

$sqlCities = 'SELECT name FROM cities WHERE country_id = :country_id ORDER BY name';
$stmt = $pdo->prepare($sqlCities);
$stmt->execute(['country_id' => $countryId]);
$citiesRows = $stmt->fetchAll();

$cities = array_column($citiesRows, 'name');

echo json_encode([
    'country' => $country,
    'cities'  => $cities,
], JSON_UNESCAPED_UNICODE);
