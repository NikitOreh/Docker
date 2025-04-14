<?php
    require 'db.php';

    $file = fopen('data.csv', 'r');

    $headers = fgetcsv($file, 1000, ','); // считывание заголовков

    while (($row = fgetcsv($file, 1000, ','))) {
        $data = array_combine($headers, $row);
        $stmt = $pdo->prepare("INSERT INTO client (client_fullname, client_phonenumber, client_mail) VALUES (?, ?, ?)");
        $stmt->execute([$data["client_name"], $data["client_phone"], $data["client_mail"]]);
        $client_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO courier (courier_fullname) VALUES (?)");
        $stmt->execute([$data["courier_name"]]);
        $courier_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO product (product_name, product_price) VALUES (?, ?)");
        $stmt->execute([$data["product"], $data["product_price"]]);
        $article = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO delivery (
                client_id, courier_id, delivery_city, delivery_street, delivery_house, delivery_entrance, delivery_apartment, delivery_floor, delivery_intercome_code, delivery_date, delivery_price, delivery_tip
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$client_id, $courier_id, $data["city"], $data["street"], $data["house"], empty($data["entrance"]) ? NULL : $data["entrance"], empty($data["apartment"]) ? NULL : $data["apartment"],
            empty($data["floor"]) ? NULL : $data["floor"], empty($data["intercome_code"]) ? NULL : $data["intercome_code"], $data["delivery_date"], $data["delivery_price"], $data["delivery_tip"]]);
        $delivery_number = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO included (product_article, delivery_number) VALUES (?, ?)");
        $stmt->execute([$article, $delivery_number]);
    }

    fclose($file);
?>