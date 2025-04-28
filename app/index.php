<?php
require 'db.php';

$stmt = $pdo->query('SELECT * FROM courier ORDER BY courier_fullname');
$courier = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query('SELECT * FROM product ORDER BY product_category');
$product = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма заказа</title>
    <link rel="stylesheet" href="styles.css">
    <script defer src="index.js"></script> 
</head>
<body>
    <div class="container">
        <h1>Интеренет магазин</h1>
        <form action="form.php" method="POST">
            <div class="form-section">
                <h3>Информация о клиенте</h3>
                <div class="form-group">
                    <label for="client_name">ФИО клиента*</label>
                    <input type="text" name="client_name" id="client_name" required maxlength=100>
                    <p class="client_name_error"></p>

                    <label for="client_phone">Номер телефона клиента*</label>
                    <input type="tel" name="client_phone" id="client_phone" data-tel-input maxlength=18 required>
                    <p class="client_phone_error"></p>
                
                    <label for="client_mail">Почта клиента</label>
                    <input type="email" name="client_mail" id="client_mail" placeholder="some@some.some">
                    <p class="email_error"></p>
            </div>

            <div class="form-section">
            <h3>Курьер</h3>
                <div class="form-group">
                    <label for="courier-select">Выберите курьера:</label>
                    <select name="courier" id="courier-select">
                        <option value="">-- Выберите курьера --</option>
                        <?php foreach ($courier as $c): ?>
                            <option value="<?= htmlspecialchars($c['courier_fullname']) ?>">
                                <?= htmlspecialchars($c['courier_fullname']) ?> 
                                (тел: <?= htmlspecialchars($c['courier_phone']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
           
                <h3>Информация о товаре</h3>
                <form method="post">
        <label for="product-select">Выберите товар:</label>
        <select name="product" id="product-select" required>
            <option value="">-- Выберите товар --</option>
            <?php foreach ($product as $p): ?>
                <option value="<?= htmlspecialchars($p['product_category']) ?>"
                    data-price="<?= htmlspecialchars($p['product_price']) ?>"
                    data-category="<?= htmlspecialchars($p['product_category']) ?>">
                    <?= htmlspecialchars($p['product_name']) ?> 
                    (<?= htmlspecialchars($p['product_category']) ?>)
                    - <?= htmlspecialchars($p['product_price']) ?> руб.
                </option>
                <?php endforeach; ?>
        </select>
    </form>
    
                <h3>Адрес доставки</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="city">Город*</label>
                        <input type="text" name="city" id="city" required>
                        <p class="city_error"></p>
                    </div>
                    <div class="form-group">
                        <label for="street">Улица*</label>
                        <input type="text" name="street" id="street" required>
                        <p class="street_error"></p>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="house">Дом*</label>
                        <input type="text" name="house" id="house" required>
                        <p class="house_error"></p>
                    </div>
                    <div class="form-group">
                        <label for="entrance">Подъезд</label>
                        <input type="number" name="entrance" id="entrance">
                        <p class="entrance_error"></p>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="apartment">Квартира</label>
                        <input type="text" name="apartment" id="apartment">
                        <p class="apartment_error"></p>
                    </div>
                    <div class="form-group">
                        <label for="floor">Этаж</label>
                        <input type="number" name="floor" id="floor" min="1">
                        <p class="floor_error"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="intercome_code">Код домофона</label>
                    <input type="text" name="intercome_code" id="intercome_code">
                </div>
        
                <h3>Информация о доставке</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="delivery_date">Дата доставки*</label>
                        <input type="date" name="delivery_date" id="delivery_date" required>
                        <p class="delivery_date_error"></p>
                    </div>
                    <div class="form-group">
                        <label for="delivery_price">Стоимость доставки*</label>
                        <input type="number" name="delivery_price" id="delivery_price" required min="1">
                        <p class="delivery_price_error"></p>
               
                    <label for="delivery_type">Тип доставки*</label>
                    <select name="delivery_type" id="delivery_type" required>
                        <option value="">Выберите тип доставки</option>
                        <option value="regular">Обычная доставка</option>
                        <option value="cargo">Грузовая доставка</option>
                    </select>
                    <p class="delivery_type_error"></p>
                </div>
            </div>

            <input type="submit" value="Отправить">
        </form>
    </div>
    <script src="phoneinput.js"></script>
    <script src="index.js"></script>
</body>
</html>