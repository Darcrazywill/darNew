<?php
// ============================================
// Лабораторная работа: Погодный информер
// API: Open-Meteo (бесплатный, без ключа)
// ============================================

$lat = 59.9386;
$lon = 30.3141;
$cityName = 'Санкт-Петербург';

function getWeather($lat, $lon) {
    $url = "https://api.open-meteo.com/v1/forecast?"
         . "latitude={$lat}"
         . "&longitude={$lon}"
         . "&current=temperature_2m,weather_code,wind_speed_10m"
         . "&daily=temperature_2m_max,temperature_2m_min,weather_code,wind_speed_10m_max"
         . "&timezone=auto"
         . "&forecast_days=7";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);

    curl_close($ch);

    if ($httpCode === 200 && $response) {
        return json_decode($response, true);
    }

    return ['error' => "HTTP {$httpCode}: {$error}"];
}

function getWeatherDescription($weatherCode) {
    $weatherCode = (int)$weatherCode;

    $codes = [
        0  => 'Ясно',
        1  => 'Малооблачно',
        2  => 'Переменная облачность',
        3  => 'Пасмурно',
        45 => 'Туман',
        48 => 'Туман с изморозью',
        51 => 'Лёгкая морось',
        53 => 'Морось',
        55 => 'Сильная морось',
        56 => 'Ледяная морось',
        57 => 'Плотная ледяная морось',
        61 => 'Небольшой дождь',
        63 => 'Дождь',
        65 => 'Сильный дождь',
        66 => 'Ледяной дождь',
        67 => 'Сильный ледяной дождь',
        71 => 'Небольшой снег',
        73 => 'Снег',
        75 => 'Сильный снег',
        77 => 'Снежная крупа',
        80 => 'Небольшой ливень',
        81 => 'Умеренный ливень',
        82 => 'Сильный ливень',
        85 => 'Небольшой снегопад',
        86 => 'Сильный снегопад',
        95 => 'Гроза',
        96 => 'Гроза с градом',
        99 => 'Сильная гроза с градом'
    ];

    return $codes[$weatherCode] ?? "Код: {$weatherCode}";
}

function getWeatherIcon($weatherCode) {
    $weatherCode = (int)$weatherCode;

    $map = [
        0  => '☀️',
        1  => '🌤️',
        2  => '⛅',
        3  => '☁️',
        45 => '🌫️',
        48 => '🌫️',
        51 => '🌦️',
        53 => '🌦️',
        55 => '🌧️',
        56 => '🌧️',
        57 => '🌧️',
        61 => '🌦️',
        63 => '🌧️',
        65 => '🌧️',
        66 => '🌧️',
        67 => '🌧️',
        71 => '❄️',
        73 => '❄️',
        75 => '❄️',
        77 => '❄️',
        80 => '🌦️',
        81 => '🌧️',
        82 => '⛈️',
        85 => '🌨️',
        86 => '🌨️',
        95 => '⛈️',
        96 => '⛈️',
        99 => '⛈️'
    ];

    return $map[$weatherCode] ?? '❓';
}

$weatherData = getWeather($lat, $lon);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Погодный информер</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Arial, sans-serif;
            color: #eef4ff;
            background:
                radial-gradient(circle at top left, rgba(72, 123, 255, 0.35), transparent 30%),
                radial-gradient(circle at bottom right, rgba(0, 191, 255, 0.28), transparent 35%),
                linear-gradient(135deg, #071120 0%, #0d1b3a 45%, #132a56 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .weather-shell {
            width: 100%;
            max-width: 980px;
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 24px;
        }

        .panel {
            background: rgba(255, 255, 255, 0.09);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 28px;
            backdrop-filter: blur(14px);
            box-shadow:
                0 20px 60px rgba(0, 0, 0, 0.28),
                inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        .main-card {
            padding: 32px;
            position: relative;
            overflow: hidden;
        }

        .main-card::before {
            content: "";
            position: absolute;
            inset: -80px auto auto -80px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(77, 163, 255, 0.35), transparent 70%);
            pointer-events: none;
        }

        .side-card {
            padding: 28px 22px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(120, 176, 255, 0.14);
            border: 1px solid rgba(149, 196, 255, 0.24);
            color: #dcecff;
            font-size: 14px;
            margin-bottom: 18px;
        }

        h1 {
            margin: 0 0 10px;
            font-size: 42px;
            line-height: 1.05;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        h2 {
            margin: 0;
            font-size: 26px;
            font-weight: 600;
        }

        h3 {
            margin: 0 0 18px;
            font-size: 22px;
            font-weight: 600;
        }

        .muted {
            color: rgba(232, 241, 255, 0.72);
        }

        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-top: 16px;
            margin-bottom: 26px;
        }

        .hero-icon {
            font-size: 86px;
            line-height: 1;
            filter: drop-shadow(0 8px 24px rgba(102, 175, 255, 0.35));
        }

        .temperature {
            font-size: 84px;
            font-weight: 800;
            line-height: 0.95;
            letter-spacing: -0.04em;
            margin-bottom: 10px;
        }

        .condition {
            font-size: 20px;
            color: #e7f1ff;
            margin-bottom: 8px;
        }

        .city {
            margin-top: 12px;
            margin-bottom: 6px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-top: 24px;
        }

        .stat {
            padding: 16px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255,255,255,0.10);
            text-align: left;
        }

        .stat-label {
            font-size: 13px;
            color: rgba(230, 240, 255, 0.65);
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 20px;
            font-weight: 700;
        }

        .forecast-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        .forecast-day {
            padding: 16px 14px;
            border-radius: 20px;
            background: linear-gradient(180deg, rgba(255,255,255,0.11), rgba(255,255,255,0.06));
            border: 1px solid rgba(255,255,255,0.10);
        }

        .forecast-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .forecast-name {
            font-size: 15px;
            font-weight: 600;
        }

        .forecast-date {
            font-size: 12px;
            color: rgba(232, 241, 255, 0.68);
        }

        .forecast-icon {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .forecast-temp {
            font-size: 20px;
            font-weight: 700;
        }

        .forecast-min {
            font-size: 13px;
            color: rgba(232, 241, 255, 0.7);
        }

        .error {
            background: rgba(220, 53, 69, 0.18);
            border: 1px solid rgba(255, 120, 140, 0.35);
            color: #ffe8ec;
            padding: 16px;
            border-radius: 18px;
        }

        @media (max-width: 900px) {
            .weather-shell {
                grid-template-columns: 1fr;
            }

            .temperature {
                font-size: 64px;
            }

            .hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .forecast-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 560px) {
            body {
                padding: 14px;
            }

            .main-card,
            .side-card {
                padding: 20px;
            }

            h1 {
                font-size: 32px;
            }

            .temperature {
                font-size: 54px;
            }

            .forecast-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="weather-shell">
        <div class="panel main-card">
            <div class="badge">● Live Weather</div>

            <?php if (isset($weatherData['error'])): ?>
                <div class="error">
                    <strong>Ошибка:</strong> <?= htmlspecialchars($weatherData['error']) ?>
                </div>
            <?php elseif (!isset($weatherData['current'])): ?>
                <div class="error">
                    <strong>Ошибка:</strong> Не удалось получить данные от API
                </div>
            <?php else:
                $current = $weatherData['current'];
                $daily = $weatherData['daily'] ?? null;
                $currentCode = $current['weather_code'] ?? 0;
            ?>
                <div class="city">
                    <h1>Погода сейчас</h1>
                    <h2><?= htmlspecialchars($cityName) ?></h2>
                    <div class="muted"><?= $lat ?>°, <?= $lon ?>°</div>
                </div>

                <div class="hero">
                    <div>
                        <div class="temperature"><?= round($current['temperature_2m'] ?? 0) ?>°C</div>
                        <div class="condition"><?= getWeatherDescription($currentCode) ?></div>
                        <div class="muted">Обновлено: <?= date('d.m.Y H:i', strtotime($current['time'] ?? 'now')) ?></div>
                    </div>

                    <div class="hero-icon"><?= getWeatherIcon($currentCode) ?></div>
                </div>

                <div class="stats">
                    <div class="stat">
                        <div class="stat-label">Скорость ветра</div>
                        <div class="stat-value"><?= round($current['wind_speed_10m'] ?? 0) ?> км/ч</div>
                    </div>

                    <div class="stat">
                        <div class="stat-label">Состояние</div>
                        <div class="stat-value"><?= getWeatherIcon($currentCode) ?> <?= getWeatherDescription($currentCode) ?></div>
                    </div>

                    <div class="stat">
                        <div class="stat-label">Час обновления</div>
                        <div class="stat-value"><?= date('H:i', strtotime($current['time'] ?? 'now')) ?></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="panel side-card">
            <h3>Прогноз на 7 дней</h3>

            <?php if (!isset($weatherData['daily']['time'])): ?>
                <div class="error">Прогноз недоступен</div>
            <?php else: ?>
                <div class="forecast-grid">
                    <?php
                    $weekDays = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

                    for ($i = 0; $i < min(7, count($weatherData['daily']['time'])); $i++):
                        $date = new DateTime($weatherData['daily']['time'][$i]);
                        $dayName = $weekDays[$date->format('N') - 1];
                        $weatherCode = $weatherData['daily']['weather_code'][$i] ?? 0;
                    ?>
                        <div class="forecast-day">
                            <div class="forecast-top">
                                <div>
                                    <div class="forecast-name"><?= $dayName ?></div>
                                    <div class="forecast-date"><?= $date->format('d.m') ?></div>
                                </div>
                                <div class="forecast-icon"><?= getWeatherIcon($weatherCode) ?></div>
                            </div>

                            <div class="forecast-temp">
                                <?= round($weatherData['daily']['temperature_2m_max'][$i] ?? 0) ?>°
                            </div>
                            <div class="forecast-min">
                                Мин: <?= round($weatherData['daily']['temperature_2m_min'][$i] ?? 0) ?>°
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
