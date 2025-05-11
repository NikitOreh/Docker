<?php
require_once __DIR__ . '/../core/Database.php';

class ClientModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    /**
     * Создание нового клиента
     */
    public function create($fullname, $phone, $email = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO client 
            (client_fullname, client_phonenumber, client_mail) 
            VALUES (?, ?, ?)
        ");
        
        $stmt->execute([$fullname, $phone, $email]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Получение клиента по ID
     */
    public function getById($clientId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM client 
            WHERE client_id = ?
        ");
        $stmt->execute([$clientId]);
        return $stmt->fetch();
    }

    /**
     * Получение всех клиентов
     */
    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT * FROM client 
            ORDER BY client_fullname
        ");
        return $stmt->fetchAll();
    }

    /**
     * Поиск клиента по телефону
     */
    public function findByPhone($phone) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM client 
            WHERE client_phonenumber = ?
        ");
        $stmt->execute([$phone]);
        return $stmt->fetch();
    }

    /**
     * Обновление данных клиента
     */
    public function update($clientId, $fullname, $phone, $email = null) {
        $stmt = $this->pdo->prepare("
            UPDATE client SET 
                client_fullname = ?, 
                client_phonenumber = ?, 
                client_mail = ? 
            WHERE client_id = ?
        ");
        return $stmt->execute([$fullname, $phone, $email, $clientId]);
    }

    /**
     * Удаление клиента
     */
    public function delete($clientId) {
        // Проверка на существование заказов
        $orders = $this->pdo->prepare("
            SELECT COUNT(*) FROM delivery 
            WHERE client_id = ?
        ");
        $orders->execute([$clientId]);
        
        if ($orders->fetchColumn() > 0) {
            throw new Exception("Нельзя удалить клиента с существующими заказами");
        }

        $stmt = $this->pdo->prepare("
            DELETE FROM client 
            WHERE client_id = ?
        ");
        return $stmt->execute([$clientId]);
    }

    /**
     * Валидация данных клиента
     */
    public function validate($fullname, $phone, $email = null) {
        $errors = [];

        if (empty($fullname) || strlen($fullname) > 100) {
            $errors[] = 'ФИО клиента обязательно (макс. 100 символов)';
        }

        if (empty($phone) || !preg_match('/^\+?\d{10,15}$/', $phone)) {
            $errors[] = 'Некорректный номер телефона';
        }

        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Некорректный email';
        }

        return $errors;
    }
}