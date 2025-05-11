<?php
require_once __DIR__ . '/../models/ClientModel.php';
require_once __DIR__ . '/../models/CourierModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/DeliveryModel.php';

class OrderController {
    private $clientModel;
    private $courierModel;
    private $productModel;
    private $deliveryModel;

    public function __construct() {
        $this->clientModel = new ClientModel();
        $this->courierModel = new CourierModel();
        $this->productModel = new ProductModel();
        $this->deliveryModel = new DeliveryModel();
    }

    /**
     * Показать форму создания заказа
     */
    public function showForm() {
        try {
            $couriers = $this->courierModel->getAll();
            $products = $this->productModel->getAll();
            
            // Проверка наличия данных
            if (empty($couriers) || empty($products)) {
                throw new Exception("Необходимо добавить курьеров и товары перед созданием заказа");
            }

            require_once __DIR__ . '/../views/orders/create.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /');
            exit();
        }
    }

    /**
     * Обработать отправку формы заказа
     */
    public function processOrder() {
        try {
            // Валидация данных
            $errors = $this->validateOrderData($_POST);
            
            if (!empty($errors)) {
                $_SESSION['form_errors'] = $errors;
                $_SESSION['old_input'] = $_POST;
                header('Location: /orders/create');
                exit();
            }

            // Создание клиента
            $clientId = $this->clientModel->create(
                $_POST['client_name'],
                $_POST['client_phone'],
                $_POST['client_mail'] ?? null
            );

            // Создание доставки
            $deliveryId = $this->deliveryModel->create([
                'client_id' => $clientId,
                'courier_id' => $_POST['courier_id'],
                'delivery_city' => $_POST['city'],
                'delivery_street' => $_POST['street'],
                'delivery_house' => $_POST['house'],
                'delivery_entrance' => $_POST['entrance'] ?? null,
                'delivery_apartment' => $_POST['apartment'] ?? null,
                'delivery_floor' => $_POST['floor'] ?? null,
                'delivery_intercome_code' => $_POST['intercom_code'] ?? null,
                'delivery_date' => $_POST['delivery_date'],
                'delivery_price' => $_POST['delivery_price'],
                'delivery_tip' => $_POST['delivery_tip'] ?? 0,
                'delivery_type' => $_POST['delivery_type']
            ]);

            // Добавление товаров к заказу
            foreach ($_POST['products'] as $productId) {
                $this->deliveryModel->addProductToDelivery($productId, $deliveryId);
            }

            $_SESSION['success'] = 'Заказ успешно создан! Номер заказа: ' . $deliveryId;
            header('Location: /orders/' . $deliveryId);
        } catch (Exception $e) {
            $_SESSION['error'] = 'Ошибка при создании заказа: ' . $e->getMessage();
            error_log($e->getMessage());
            header('Location: /orders/create');
            exit();
        }
    }

    /**
     * Показать детали заказа
     */
    public function show($orderId) {
        try {
            $order = $this->deliveryModel->getById($orderId);
            if (!$order) {
                throw new Exception("Заказ не найден");
            }

            $order['products'] = $this->productModel->getByDeliveryId($orderId);
            $order['client'] = $this->clientModel->getById($order['client_id']);
            $order['courier'] = $this->courierModel->getById($order['courier_id']);

            require_once __DIR__ . '/../views/orders/show.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /orders');
            exit();
        }
    }

    /**
     * Валидация данных заказа
     */
    private function validateOrderData($data) {
        $errors = [];

        // Валидация клиента
        if (empty($data['client_name'])) {
            $errors['client_name'] = 'ФИО клиента обязательно';
        } elseif (preg_match('/\d/', $data['client_name'])) {
            $errors['client_name'] = 'ФИО не должно содержать цифр';
        }

        if (empty($data['client_phone']) || !preg_match('/^\+?\d{10,15}$/', $data['client_phone'])) {
            $errors['client_phone'] = 'Некорректный номер телефона';
        }

        // Валидация адреса
        if (empty($data['city'])) {
            $errors['city'] = 'Город обязателен';
        }

        if (empty($data['street'])) {
            $errors['street'] = 'Улица обязательна';
        }

        if (empty($data['house'])) {
            $errors['house'] = 'Номер дома обязателен';
        }

        // Валидация доставки
        if (empty($data['delivery_date'])) {
            $errors['delivery_date'] = 'Дата доставки обязательна';
        } elseif (strtotime($data['delivery_date']) < strtotime('today')) {
            $errors['delivery_date'] = 'Дата доставки не может быть в прошлом';
        }

        if (empty($data['delivery_price']) || !is_numeric($data['delivery_price']) || $data['delivery_price'] <= 0) {
            $errors['delivery_price'] = 'Некорректная стоимость доставки';
        }

        if (empty($data['courier_id'])) {
            $errors['courier_id'] = 'Выберите курьера';
        }

        if (empty($data['products'])) {
            $errors['products'] = 'Выберите хотя бы один товар';
        }

        return $errors;
    }
}