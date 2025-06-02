<?php
require 'db.php';

$stmt = $pdo->query('SELECT * FROM delivery ORDER BY delivery_number');
$delivery = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Данные</title>
    <style>
        table {
            width: 100%;
            max-width: 90%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table th,
        table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 16px;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        table td {
            color: #333;
            font-size: 14px;
        }

        table td:empty::before {
            content: "—";
            color: #999;
        }
    </style>
</head>

<body>
    <h2>Заказы</h2>
    <div class="form-section">
                <!-- <h3>Курьер</h3>
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
                 <div class="filter-section">
                    <form method="get">
                        <label for="category">Фильтр по категории:</label>
                        <select name="category" id="category" onchange="this.form.submit()">
                            <option value="all">Все категории</option>
                            <?php foreach ($categories as $ca): ?>
                                <option value="<?= htmlspecialchars($ca) ?>" 
                                    <?= ($selectedCategory === $ca) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ca) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div> -->
     
    <!-- <form method="post">
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
     -->
    <table>
        <tr>
            <th>Номер доставки</th>
            <th>ФИО клиента</th>
            <th>ФИО курьера</th>
            <th>Город</th>
            <th>Улица</th>
            <th>Дом</th>
            <th>Подъезд</th>
            <th>Квартира</th>
            <th>Этаж</th>
            <th>Код домофона</th>
            <th>Дата доставки</th>
            <th>Стоимость доставки</th>
            <th>Тип доставки</th>
        </tr>
        <?php foreach ($delivery as $d): ?>
            <tr>
                <td><?= $d['delivery_number'] ?></td>
                <td><?= $d['client_fullname'] ?></td>
                <td><?= $d['courier_fullname'] ?></td>
                <td><?= $d['delivery_city'] ?></td>
                <td><?= $d['delivery_street'] ?></td>
                <td><?= $d['delivery_house'] ?></td>
                <td><?= empty($d['delivery_entrance']) ? "Не указан" : $d['delivery_entrance'] ?></td>
                <td><?= empty($d['delivery_apartment']) ? "Не указан" : $d['delivery_apartment']  ?></td>
                <td><?= empty($d['delivery_floor']) ? "Не указан" : $d['delivery_floor'] ?></td>
                <td><?= empty($d['delivery_intercome_code']) ? "Не указан" : $d['delivery_intercome_code'] ?></td>
                <td><?= $d['delivery_date'] ?></td>
                <td><?= $d['delivery_price'] ?></td>
                <td><?= $d['delivery_type'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <div style="margin: 10px 0;">
    <form action="report.php" method="get" target="_blank">
        <button type="submit" name="format" value="pdf">Скачать PDF отчёт</button>
        <button type="submit" name="format" value="excel">Скачать Excel отчёт</button>
    </form>
</div>
</body>

</html>