<?php
require_once __DIR__ . '/../core/Database.php';

class ProductModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    /**
     * Создание нового товара
     */
    public function create($name, $price, $category = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO product 
            (product_name, product_price, product_category) 
            VALUES (?, ?, ?)
        ");
        
        $stmt->execute([$name, $price, $category]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Получение товара по ID
     */
    public function getById($productId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM product 
            WHERE product_article = ?
        ");
        $stmt->execute([$productId]);
        return $stmt->fetch();
    }

    /**
     * Получение всех товаров с фильтрацией по категории
     */
    public function getAll($category = null) {
        if ($category) {
            $stmt = $this->pdo->prepare("
                SELECT * FROM product 
                WHERE product_category = ?
                ORDER BY product_name
            ");
            $stmt->execute([$category]);
        } else {
            $stmt = $this->pdo->query("
                SELECT * FROM product 
                ORDER BY product_category, product_name
            ");
        }
        return $stmt->fetchAll();
    }

    /**
     * Получение уникальных категорий товаров
     */
    public function getCategories() {
        $stmt = $this->pdo->query("
            SELECT DISTINCT product_category 
            FROM product 
            WHERE product_category IS NOT NULL
            ORDER BY product_category
        ");
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    /**
     * Обновление данных товара
     */
    public function update($productId, $name, $price, $category = null) {
        $stmt = $this->pdo->prepare("
            UPDATE product SET 
                product_name = ?, 
                product_price = ?,
                product_category = ?
            WHERE product_article = ?
        ");
        return $stmt->execute([$name, $price, $category, $productId]);
    }

    /**
     * Удаление товара
     */
    public function delete($productId) {
        // Проверка на существование в заказах
        $orders = $this->pdo->prepare("
            SELECT COUNT(*) FROM included 
            WHERE product_article = ?
        ");
        $orders->execute([$productId]);
        
        if ($orders->fetchColumn() > 0) {
            throw new Exception("Нельзя удалить товар, который есть в заказах");
        }

        $stmt = $this->pdo->prepare("
            DELETE FROM product 
            WHERE product_article = ?
        ");
        return $stmt->execute([$productId]);
    }

    /**
     * Поиск товаров по названию
     */
    public function searchByName($query) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM product 
            WHERE product_name LIKE ?
            ORDER BY product_name
        ");
        $stmt->execute(["%$query%"]);
        return $stmt->fetchAll();
    }

    /**
     * Валидация данных товара
     */
    public function validate($name, $price, $category = null) {
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Название товара обязательно';
        } elseif (strlen($name) > 100) {
            $errors[] = 'Название слишком длинное (макс. 100 символов)';
        }

        if (!is_numeric($price) || $price <= 0) {
            $errors[] = 'Цена должна быть положительным числом';
        }

        if ($category && strlen($category) > 50) {
            $errors[] = 'Название категории слишком длинное (макс. 50 символов)';
        }

        return $errors;
    }

    /**
     * Получение популярных товаров
     */
    public function getPopularProducts($limit = 5) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, COUNT(i.product_article) as order_count
            FROM product p
            LEFT JOIN included i ON p.product_article = i.product_article
            GROUP BY p.product_article
            ORDER BY order_count DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}