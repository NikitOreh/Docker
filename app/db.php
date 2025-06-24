<?php 
    $host = 'db';
    $dbname = 'sklad';
    $user = 'root';
    $pass = 'rootpass';

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
