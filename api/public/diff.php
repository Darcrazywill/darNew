<?php
header('Content-Type: text/plain; charset=utf-8');
$date1Str = $_GET['date1'] ?? null;
$date2Str = $_GET['date2'] ?? null;

if (!$date1Str || !$date2Str) {
    http_response_code(400);
    echo "Нужно передать параметры date1 и date2 в формате ГГГГ-ММ-ДД.";
    exit;
}

$pattern = '/^\d{4}-\d{2}-\d{2}$/';
if (!preg_match($pattern, $date1Str) || !preg_match($pattern, $date2Str)) {
    http_response_code(400);
    echo "Неверный формат даты. Ожидается ГГГГ-ММ-ДД, например: 2026-04-30.";
    exit;
}

$date1 = DateTime::createFromFormat('Y-m-d', $date1Str);
$date2 = DateTime::createFromFormat('Y-m-d', $date2Str);

$errors1 = DateTime::getLastErrors();
$errors2 = DateTime::getLastErrors();
if ($errors1['warning_count'] > 0 || $errors1['error_count'] > 0 ||
    $errors2['warning_count'] > 0 || $errors2['error_count'] > 0) {
    http_response_code(400);
    echo "Одна из дат некорректна.";
    exit;
}

$interval = $date1->diff($date2);

$days = $interval->days;

echo $days;
