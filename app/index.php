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
                        <?php foreach ($courier as $courier): ?>
                            <option value="<?= htmlspecialchars($courier['courier_email']) ?>">
                                <?= htmlspecialchars($courier['courier_fullname']) ?> 
                                (тел: <?= htmlspecialchars($courier['courier_phone']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
           
                <h3>Информация о товаре</h3>
                <div class="filter-section">
                    <form method="get">
                        <label for="category">Фильтр по категории:</label>
                        <select name="category" id="category" onchange="this.form.submit()">
                            <option value="all">Все категории</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category) ?>" 
                                    <?= ($selectedCategory === $category) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
    
    <form method="post">
        <label for="product-select">Выберите товар:</label>
        <select name="product" id="product-select" required>
            <option value="">-- Выберите товар --</option>
            <?php foreach ($products as $product): ?>
                <option value="<?= htmlspecialchars($product['product_name']) ?>"
                    data-price="<?= htmlspecialchars($product['product_price']) ?>"
                    data-category="<?= htmlspecialchars($product['product_category']) ?>">
                    <?= htmlspecialchars($product['product_name']) ?> 
                    (<?= htmlspecialchars($product['product_category']) ?>)
                    - <?= htmlspecialchars($product['product_price']) ?> руб.
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