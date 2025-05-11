<?php
require_once __DIR__ . '/../core/Database.php';

class DeliveryModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    /**
     * Создание новой доставки
     */
    public function getRecentDeliveries(int $limit = 5): array
{
    $stmt = $this->pdo->prepare("
        SELECT d.*, c.client_fullname, cr.courier_fullname 
        FROM delivery d
        JOIN client c ON d.client_id = c.client_id
        JOIN courier cr ON d.courier_id = cr.courier_id
        ORDER BY d.created_at DESC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

public function getDeliveryCountByDate(string $date): int
{
    $stmt = $this->pdo->prepare("
        SELECT COUNT(*) 
        FROM delivery 
        WHERE DATE(delivery_date) = ?
    ");
    $stmt->execute([$date]);
    return $stmt->fetchColumn();
}

public function getDeliveryCountByDateRange(string $startDate, string $endDate): int
{
    $stmt = $this->pdo->prepare("
        SELECT COUNT(*) 
        FROM delivery 
        WHERE delivery_date BETWEEN ? AND ?
    ");
    $stmt->execute([$startDate, $endDate]);
    return $stmt->fetchColumn();
}

    public function create(array $deliveryData) {
        $stmt = $this->pdo->prepare("
            INSERT INTO delivery (
                client_id, courier_id, delivery_city, delivery_street,
                delivery_house, delivery_entrance, delivery_apartment,
                delivery_floor, delivery_intercome_code, delivery_date,
                delivery_price, delivery_tip, delivery_type
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $deliveryData['client_id'],
            $deliveryData['courier_id'],
            $deliveryData['delivery_city'],
            $deliveryData['delivery_street'],
            $deliveryData['delivery_house'],
            $deliveryData['delivery_entrance'],
            $deliveryData['delivery_apartment'],
            $deliveryData['delivery_floor'],
            $deliveryData['delivery_intercome_code'],
            $deliveryData['delivery_date'],
            $deliveryData['delivery_price'],
            $deliveryData['delivery_tip'],
            $deliveryData['delivery_type']
        ]);
        
        return $this->pdo->lastInsertId();
    }

    /**
     * Получение доставки по ID
     */
    public function getById($deliveryId) {
        $stmt = $this->pdo->prepare("
            SELECT d.*, 
                   c.client_fullname, c.client_phonenumber, c.client_mail,
                   cr.courier_fullname, cr.courier_phone
            FROM delivery d
            JOIN client c ON d.client_id = c.client_id
            JOIN courier cr ON d.courier_id = cr.courier_id
            WHERE d.delivery_number = ?
        ");
        $stmt->execute([$deliveryId]);
        return $stmt->fetch();
    }

    /**
     * Получение всех доставок с пагинацией
     */
    public function getAll($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->pdo->prepare("
            SELECT d.*, 
                   c.client_fullname,
                   cr.courier_fullname
            FROM delivery d
            JOIN client c ON d.client_id = c.client_id
            JOIN courier cr ON d.courier_id = cr.courier_id
            ORDER BY d.delivery_date DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Получение доставок по дате
     */
    public function getByDate($date) {
        $stmt = $this->pdo->prepare("
            SELECT d.*, 
                   c.client_fullname,
                   cr.courier_fullname
            FROM delivery d
            JOIN client c ON d.client_id = c.client_id
            JOIN courier cr ON d.courier_id = cr.courier_id
            WHERE DATE(d.delivery_date) = ?
            ORDER BY d.delivery_date
        ");
        $stmt->execute([$date]);
        return $stmt->fetchAll();
    }

    /**
     * Добавление товара к доставке
     */
    public function addProductToDelivery($productId, $deliveryId) {
        $stmt = $this->pdo->prepare("
            INSERT INTO included (product_article, delivery_number)
            VALUES (?, ?)
        ");
        return $stmt->execute([$productId, $deliveryId]);
    }

    /**
     * Получение товаров для доставки
     */
    public function getProductsForDelivery($deliveryId) {
        $stmt = $this->pdo->prepare("
            SELECT p.* 
            FROM product p
            JOIN included i ON p.product_article = i.product_article
            WHERE i.delivery_number = ?
        ");
        $stmt->execute([$deliveryId]);
        return $stmt->fetchAll();
    }

    /**
     * Обновление статуса доставки
     */
    public function updateStatus($deliveryId, $status) {
        $stmt = $this->pdo->prepare("
            UPDATE delivery 
            SET delivery_status = ? 
            WHERE delivery_number = ?
        ");
        return $stmt->execute([$status, $deliveryId]);
    }

    /**
     * Получение количества доставок
     */
    public function getCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM delivery");
        return $stmt->fetchColumn();
    }

    /**
     * Поиск доставок по клиенту
     */
    public function findByClient($clientId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM delivery 
            WHERE client_id = ?
            ORDER BY delivery_date DESC
        ");
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }

    /**
     * Валидация данных доставки
     */
    public function validate(array $data) {
        $errors = [];

        if (empty($data['delivery_city'])) {
            $errors[] = 'Город доставки обязателен';
        }

        if (empty($data['delivery_street'])) {
            $errors[] = 'Улица доставки обязательна';
        }

        if (empty($data['delivery_house'])) {
            $errors[] = 'Номер дома обязателен';
        }

        if (empty($data['delivery_date'])) {
            $errors[] = 'Дата доставки обязательна';
        } elseif (strtotime($data['delivery_date']) < time()) {
            $errors[] = 'Дата доставки не может быть в прошлом';
        }

        if (!isset($data['delivery_price']) || $data['delivery_price'] <= 0) {
            $errors[] = 'Некорректная стоимость доставки';
        }

        return $errors;
    }
}