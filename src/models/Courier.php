<?php
require_once __DIR__ . '/../core/Database.php';

class CourierModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    /**
     * Создание нового курьера
     */
    public function create($fullname, $phone = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO courier 
            (courier_fullname, courier_phone) 
            VALUES (?, ?)
        ");
        
        $stmt->execute([$fullname, $phone]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Получение курьера по ID
     */
    public function getById($courierId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM courier 
            WHERE courier_id = ?
        ");
        $stmt->execute([$courierId]);
        return $stmt->fetch();
    }

    /**
     * Получение всех курьеров
     */
    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT * FROM courier 
            ORDER BY courier_fullname
        ");
        return $stmt->fetchAll();
    }

    /**
     * Получение активных курьеров (с назначенными доставками)
     */
    public function getActiveCouriers() {
        $stmt = $this->pdo->query("
            SELECT DISTINCT c.* 
            FROM courier c
            JOIN delivery d ON c.courier_id = d.courier_id
            WHERE d.delivery_date >= CURDATE()
            ORDER BY c.courier_fullname
        ");
        return $stmt->fetchAll();
    }

    /**
     * Обновление данных курьера
     */
    public function update($courierId, $fullname, $phone = null) {
        $stmt = $this->pdo->prepare("
            UPDATE courier SET 
                courier_fullname = ?, 
                courier_phone = ? 
            WHERE courier_id = ?
        ");
        return $stmt->execute([$fullname, $phone, $courierId]);
    }

    /**
     * Удаление курьера
     */
    public function delete($courierId) {
        // Проверка на существование заказов
        $orders = $this->pdo->prepare("
            SELECT COUNT(*) FROM delivery 
            WHERE courier_id = ?
        ");
        $orders->execute([$courierId]);
        
        if ($orders->fetchColumn() > 0) {
            throw new Exception("Нельзя удалить курьера с назначенными доставками");
        }

        $stmt = $this->pdo->prepare("
            DELETE FROM courier 
            WHERE courier_id = ?
        ");
        return $stmt->execute([$courierId]);
    }

    /**
     * Валидация данных курьера
     */
    public function validate($fullname, $phone = null) {
        $errors = [];

        if (empty($fullname) {
            $errors[] = 'ФИО курьера обязательно';
        } elseif (strlen($fullname) > 100) {
            $errors[] = 'ФИО курьера слишком длинное (макс. 100 символов)';
        }

        if ($phone && !preg_match('/^\+?\d{10,15}$/', $phone)) {
            $errors[] = 'Некорректный номер телефона';
        }

        return $errors;
    }

    /**
     * Получение статистики по курьеру
     */
    public function getStats($courierId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(*) as total_deliveries,
                SUM(delivery_price) as total_earnings,
                SUM(delivery_tip) as total_tips
            FROM delivery
            WHERE courier_id = ?
        ");
        $stmt->execute([$courierId]);
        return $stmt->fetch();
    }
}