<?php
    // Функции валидации
    function hasNumber($str) {
        return preg_match('/\d/', $str);
    }

    function hasLetter($str) {
        return preg_match('/[a-zA-Zа-яА-Я]/u', $str);
    }

    function hasSpecial($str) {
        return preg_match('/[^a-zA-Z0-9а-яА-Я]/u', $str);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Получаем и обрабатываем данные
        $product = trim($_POST['product'] ?? '');
        $product_price = trim($_POST['price'] ?? '');
        $label = trim($_POST['label'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $street = trim($_POST['street'] ?? '');
        $house = trim($_POST['house'] ?? '');
        $entrance = trim($_POST['entrance'] ?? '');
        $apartment = trim($_POST['apartment'] ?? '');
        $floor = trim($_POST['floor'] ?? '');
        $intercom_code = trim($_POST['intercom_code'] ?? '');

        // Массив для ошибок
        $errors = [];

        // Валидация
        if (empty($product)) {
            $errors[] = 'Укажите название товара';
        }

        if (empty($product_price) || !is_numeric($product_price) || $product_price < 1) {
            $errors[] = 'Укажите корректную цену товара';
        }

        if (empty($label)) {
            $errors[] = 'Укажите метку товара';
        }

        if (empty($category)) {
            $errors[] = 'Укажите категорию товара';
        }

        if (empty($street)) {
            $errors[] = 'Укажите улицу';
        }

        if (empty($house) || !is_numeric($house) || hasSpecial($house)) {
            $errors[] = 'Укажите корректный номер дома';
        }

        if (!empty($entrance) && (!is_numeric($entrance) || hasLetter($entrance))) {
            $errors[] = 'Укажите корректный номер подъезда';
        }

        if (empty($apartment) || !is_numeric($apartment) || hasSpecial($apartment)) {
            $errors[] = 'Укажите корректный номер квартиры';
        }

        if (!empty($floor) && (!is_numeric($floor) || hasLetter($floor))) {
            $errors[] = 'Укажите корректный этаж';
        }

        if (!empty($intercom_code) && hasLetter($intercom_code)) {
            $errors[] = 'Укажите корректный код домофона';
        }

        }

        // Файл для сохранения данных
        $csvFile = 'data.csv';
        $dataRow = [
            $product,
            $product_price,
            $label,
            $category,
            $street,
            $house,
            $entrance,
            $apartment,
            $floor,
            $intercom_code,
        ];

        if (!empty($errors)) {
            echo 'Данные не сохранены' . "\n";
            foreach ($errors as $error) {
                echo $error . "\n";
            }
            exit();
        }

        if (($file = fopen($csvFile, 'a'))) {
            fputcsv($file, $dataRow);
            fclose($file);
            $message = 'Данные успешно сохранены';
        }

        echo $message . "\n";
?>
