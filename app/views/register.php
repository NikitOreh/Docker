<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Регистрация</h1>
    <form method="POST">
        <label for="name"></label>Имя<input type="text" name='name' id="name">
        <br>
        <label for="phonenumber">Номер телефона</label><input type="text" name="phonenumber" id="phonenumber">
        <br>
        <label for="email">Email</label><input type="text" name="email" id="email">
        <br>
        <label for="password">Пароль</label><input type="password" name="password" id="password">
        <br>
        <button type="submit" formaction="/views/login.php">Зарегистрироваться</button>
    </form>
</body>

</html>