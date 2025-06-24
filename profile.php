<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Профиль пользователя</h1>
    <p>Информация о пользователе</p>
    <p>Полное имя <?= $_SESSION['client_fullname']?> </p>
    <p>Номер телефона <?= $_SESSION['client_phone']?> </p>
    <p>Почта <?= $_SESSION['client_email']?> </p>
    <p><a href="/deliveries">Доставки</a></p>
    <p><a href="/logout">Выйти</a></p>
</body>
</html>