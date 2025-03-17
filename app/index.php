<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма заказа</title>
    <link rel="stylesheet" href="styles.css">  <!-- Подключаем CSS -->
    <script defer src="index.js"></script> <!-- Подключаем JS -->
</head>
<body>

    <form id="form" action="form_handler.php" method="POST">
        <label>Товар: <input type="text" id="product" name="product"></label>
        <span class="product_error error"></span><br>

        <label>Цена: <input type="text" id="price" name="price" class="only-numbers"></label>
        <span class="price_error error"></span><br>

        <label>Артикул: <input type="text" id="article" name="article" class="article-format"></label>
        <span class="article_error error"></span><br>

        <label>Категория: <input type="text" id="category" name="category"></label>
        <span class="category_error error"></span><br>

        <label>Телефон: <input type="text" id="phone" name="phone" class="phone-format"></label>
        <span class="phone_error error"></span><br>

        <label>Улица: <input type="text" id="street" name="street"></label>
        <span class="street_error error"></span><br>

        <label>Дом: <input type="text" id="house" name="house" class="only-numbers"></label>
        <span class="house_error error"></span><br>

        <label>Квартира: <input type="text" id="apartment" name="apartment" class="only-numbers"></label>
        <span class="apartment_error error"></span><br>

        <label>Этаж: <input type="text" id="floor" name="floor" class="only-numbers"></label>
        <span class="floor_error error"></span><br>

        <button type="submit">Отправить</button>
    </form>

</body>
</html>
