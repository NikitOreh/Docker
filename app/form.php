<?php

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
    $product = trim($_POST['product'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $label = trim($_POST['label'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $street = trim($_POST['street'] ?? '');
    $house = trim($_POST['house'] ?? '');
    $entrance = trim($_POST['entrance'] ?? '');
    $apartment = trim($_POST['apartment'] ?? '');
    $floor = trim($_POST['floor'] ?? '');
    $intercom_code = trim($_POST['intercom_code'] ?? '');
}
    $errors = [];

    if (empty($product)) {
        array_push($errors, ['field' => 'product', 'message' => 'Укажите название товара']);
    }
    if (empty($price) || !is_numeric($price) || $price < 1) {
        array_push($errors, ['field' => 'price', 'message' => 'Укажите корректную цену товара']);
    }
    if (empty($label)) {
        array_push($errors, ['field' => 'label', 'message' => 'Укажите метку товара']);
    }
    if (empty($category)) {
        array_push($errors, ['field' => 'category', 'message' => 'Укажите категорию товара']);
    }

    if (empty($street)) {
        array_push($errors, ['field' => 'street', 'message' => 'Укажите улицу']);
    }
    if (empty($house) || !is_numeric($house) || hasLetter($house)) {
        array_push($errors, ['field' => 'house', 'message' => 'Укажите корректный номер дома']);
    }
    if (!empty($entrance) && (!is_numeric($entrance) || hasLetter($entrance))) {
        array_push($errors, ['field' => 'entrance', 'message' => 'Укажите корректный номер подъезда']);
    }
    if (empty($apartment) || !is_numeric($apartment) || hasLetter($apartment)) {
        array_push($errors, ['field' => 'apartment', 'message' => 'Укажите корректный номер квартиры']);
    }
    if (!empty($floor) && (!is_numeric($floor) || hasLetter($floor))) {
        array_push($errors, ['field' => 'floor', 'message' => 'Укажите корректный этаж']);
    }
    if (!empty($intercom_code) && hasLetter($intercom_code)) {
        array_push($errors, ['field' => 'intercom_code', 'message' => 'Укажите корректный код домофона']);
    }

    if (!empty($errors)) {
        echo json_encode(["status" => "error", "errors" => $errors], JSON_UNESCAPED_UNICODE);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Получаем и обрабатываем данные
        $client_name = trim($_POST['client_name'] ?? '');
        $client_phone = str_replace($_POST['client_phone'] ?? '');
        $client_mail = trim($_POST['client_mail'] ?? '');
        $courier_name = trim($_POST['courier_name'] ?? '');
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
        if (empty($client_name) || strlen($client_name) > 100 || hasNumber($client_name)) {
            $errors[] = 'Укажите корректное имя клиента';
        }
    
        if (empty($client_phone) || strlen($client_phone) < 11) {
            $errors[] = 'Укажите корректный номер телефона';
        }
    
        if (empty($courier_name) || strlen($courier_name) > 100 || hasNumber($courier_name)) {
            $errors[] = 'Укажите корректное имя курьера';
        }
    
        if (empty($client_mail) || !isValidEmailDomain($client_mail)) {
            $errors[] = "Укажите корректную почту";
        }

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
            $client_name,
            $client_phone,
            $client_mail,
            $courier_name,
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
