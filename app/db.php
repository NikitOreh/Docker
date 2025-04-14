<?php 
    $host = 'db';
    $dbname = 'mydb';
    $user = 'user';
    $pass = 'userpass';

    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8",
            $user,
            $pass
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Ошибка подключения: " . $e->getMessage());
    }
?>