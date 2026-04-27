<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" href="/assets/images/image.png" type="image/png">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="page page-home">
        <section class="hero container">
            <div class="hero__content">
                <span class="hero__badge">Cube Dragon Studio</span>
                <h1>Добро пожаловать!</h1>
                <p>
                    Мы создаём удобные и красивые веб-решения. 
                    Здесь вы можете узнать больше о проекте и связаться с нами через форму обратной связи.
                </p>
                <div class="hero__actions">
                    <a class="btn btn-primary" href="/pages/contact.php">Связаться с нами</a>
                    <a class="btn btn-secondary" href="/pages/about.php">О нас</a>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
