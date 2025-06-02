<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Добро пожаловать</h1>
    <?php if (isset($_SESSION['user_id'])): ?>
        <p>Вы вошли как <?= htmlspecialchars($_SESSION['email']); ?></p>
        <p><a href="/views/profile.php">Профиль</a></p>
        <p><a href="/views/deliveries.php">Доставки</a></p>
        <p><a href="/views/logout.php">Выйти</a></p>
    <?php else: ?>
        <p><a href="/views/login.php">Войти</a></p>
        <p><a href="/views/register.php">Зарегистрироваться</a></p>
    <?php endif; ?>
</body>
</html>