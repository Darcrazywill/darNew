<?php
header('Content-Type: text/plain; charset=utf-8');

$dateStr = $_GET['date'] ?? null;

if (!$dateStr) {
    http_response_code(400);
    echo "Нужно передать параметр date в формате ГГГГ-ММ-ДД, например: ?date=2026-04-30";
    exit;
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
    http_response_code(400);
    echo "Неверный формат даты. Ожидается ГГГГ-ММ-ДД, например: 2026-04-30";
    exit;
}

$date = DateTime::createFromFormat('Y-m-d', $dateStr);
$errors = DateTime::getLastErrors();
if ($errors['warning_count'] > 0 || $errors['error_count'] > 0) {
    http_response_code(400);
    echo "Неверная дата";
    exit;
}

$weekdayNumber = (int)$date->format('N');

$weekdays = [
    1 => 'Понедельник',
    2 => 'Вторник',
    3 => 'Среда',
    4 => 'Четверг',
    5 => 'Пятница',
    6 => 'Суббота',
    7 => 'Воскресенье',
];

echo $weekdays[$weekdayNumber];
