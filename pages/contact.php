<?php
$name = '';
$email = '';
$message = '';

$nameError = '';
$emailError = '';
$messageError = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '') {
        $nameError = 'Введите имя.';
    }

    if ($email === '') {
        $emailError = 'Введите email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = 'Введите корректный email.';
    }

    if ($message === '') {
        $messageError = 'Введите сообщение.';
    }

    if ($nameError === '' && $emailError === '' && $messageError === '') {
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $safeEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        $line = "Имя: {$safeName} | Email: {$safeEmail} | Сообщение: {$safeMessage}" . PHP_EOL;
        $logFile = __DIR__ . '/../messages.txt';
        file_put_contents($logFile, $line, FILE_APPEND);

        $successMessage = 'Сообщение успешно отправлено.';
        $name = '';
        $email = '';
        $message = '';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Контакты</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" href="/assets/images/image.png" type="image/png">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="page container">
        <section class="contact-layout">
            <div class="contact-info card">
                <span class="section-label">Связь с нами</span>
                <h1>Контакты</h1>
                <p>
                    Заполните форму, и мы ответим вам в ближайшее время.
                    Все поля обязательны для заполнения.
                </p>

                <ul class="contact-list">
                    <li>Email: hello@example.com</li>
                    <li>Телефон: +7 (900) 123-45-67</li>
                    <li>Город: Санкт-Петербург</li>
                </ul>
            </div>

            <div class="contact-form-wrapper card">
                <?php if ($successMessage !== ''): ?>
                    <div class="alert alert-success">
                        <?= $successMessage; ?>
                    </div>
                <?php endif; ?>

                <form method="post" class="contact-form" novalidate>
                    <div class="form-group">
                        <label for="name">Имя</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="Введите ваше имя"
                            value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"
                        >
                        <?php if ($nameError !== ''): ?>
                            <div class="error-text"><?= $nameError; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Введите ваш email"
                            value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
                        >
                        <?php if ($emailError !== ''): ?>
                            <div class="error-text"><?= $emailError; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="message">Сообщение</label>
                        <textarea
                            id="message"
                            name="message"
                            placeholder="Введите ваше сообщение"
                            rows="6"
                        ><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <?php if ($messageError !== ''): ?>
                            <div class="error-text"><?= $messageError; ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">
                        Отправить
                    </button>
                </form>
            </div>
        </section>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
