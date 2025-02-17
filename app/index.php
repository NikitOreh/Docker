<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Интеренет-магазин электроники</h1>
    <form action="form.php" method="POST">
    <label for="product">Товар</label>
        <input type="text" name="product" id="product">
        <br><br>
        <label for="price">Стоимость товара</label>
        <input type="number" name="price" id="price">
        <br><br>
        <label for="label">Производитель</label>
        <input type="text" name="label" id="label">
        <br><br>
        <label for="category">Категория</label>
        <input type="email" name="category" id="category">
        <br><br>
        <label for="address">Адрес доставки</label>
        <input type="text" name="address" id="address">
        <br><br>
        <label for="date">Дата доставки</label>
        <input type="date" name="date" id="date">
        <br><br>
        <input type="submit" value="Отправить">
    </form>
</body>
</html>