<?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product = trim($_POST['product'] ?? '');
            $product_price = trim($_POST['price'] ?? '');
            $label = trim($_POST['label'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $date = trim($_POST['date'] ?? '');

            $csvFile = 'data.csv';
            $dataRow = [
                $product,
                $price,
                $label,
                $category,
                $address,
                $date,
            ];

            if (($file = fopen($csvFile, 'a'))) {
                fputcsv($file, $dataRow);
                fclose($file);
                $message = 'Даныне успешно сохранены';
            }
            else {
                $message = 'Ошибка при сохранении данных';
            }
        }
    ?>