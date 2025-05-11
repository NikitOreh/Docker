<?php
require_once __DIR__ . '/../models/ClientModel.php';
require_once __DIR__ . '/../models/CourierModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/DeliveryModel.php';

class ImportController {
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
     * Обработка импорта данных из CSV
     */
    public function handleImport() {
        try {
            // Проверка наличия файла
            if (!file_exists('data.csv')) {
                throw new Exception("Файл data.csv не найден");
            }

            $file = fopen('data.csv', 'r');
            $headers = fgetcsv($file, 1000, ','); // Читаем заголовки

            // Валидация заголовков
            $requiredHeaders = [
                'client_name', 'client_phone', 'client_mail',
                'courier_name', 'product', 'product_price',
                'city', 'street', 'house', 'entrance',
                'apartment', 'floor', 'intercome_code',
                'delivery_date', 'delivery_price', 'delivery_tip'
            ];

            if (count(array_intersect($headers, $requiredHeaders)) < count($requiredHeaders)) {
                throw new Exception("Неверный формат CSV файла");
            }

            $importedCount = 0;
            $errors = [];

            while (($row = fgetcsv($file, 1000, ','))) {
                try {
                    $data = array_combine($headers, $row);

                    // Валидация данных
                    $this->validateImportData($data);

                    // Создание клиента
                    $clientId = $this->clientModel->create(
                        $data['client_name'],
                        $data['client_phone'],
                        $data['client_mail']
                    );

                    // Создание курьера
                    $courierId = $this->courierModel->create(
                        $data['courier_name']
                    );

                    // Создание продукта
                    $productId = $this->productModel->create(
                        $data['product'],
                        $data['product_price']
                    );

                    // Создание доставки
                    $deliveryId = $this->deliveryModel->create([
                        'client_id' => $clientId,
                        'courier_id' => $courierId,
                        'delivery_city' => $data['city'],
                        'delivery_street' => $data['street'],
                        'delivery_house' => $data['house'],
                        'delivery_entrance' => empty($data['entrance']) ? null : $data['entrance'],
                        'delivery_apartment' => empty($data['apartment']) ? null : $data['apartment'],
                        'delivery_floor' => empty($data['floor']) ? null : $data['floor'],
                        'delivery_intercome_code' => empty($data['intercome_code']) ? null : $data['intercome_code'],
                        'delivery_date' => $data['delivery_date'],
                        'delivery_price' => $data['delivery_price'],
                        'delivery_tip' => empty($data['delivery_tip']) ? 0 : $data['delivery_tip']
                    ]);

                    // Связь продукта с доставкой
                    $this->deliveryModel->addProductToDelivery($productId, $deliveryId);

                    $importedCount++;
                } catch (Exception $e) {
                    $errors[] = "Ошибка в строке " . ($importedCount + 1) . ": " . $e->getMessage();
                    continue;
                }
            }

            fclose($file);

            // Формируем ответ
            $response = [
                'status' => 'success',
                'imported' => $importedCount,
                'errors' => $errors
            ];

            header('Content-Type: application/json');
            echo json_encode($response, JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Валидация данных импорта
     */
    private function validateImportData($data) {
        $requiredFields = [
            'client_name', 'client_phone',
            'courier_name', 'product',
            'city', 'street', 'house',
            'delivery_date', 'delivery_price'
        ];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Поле {$field} обязательно для заполнения");
            }
        }

        if (!preg_match('/^\+?\d{10,15}$/', $data['client_phone'])) {
            throw new Exception("Некорректный формат телефона");
        }

        if (!empty($data['client_mail']) && !filter_var($data['client_mail'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Некорректный email");
        }

        if (!is_numeric($data['product_price']) || $data['product_price'] <= 0) {
            throw new Exception("Цена продукта должна быть положительным числом");
        }

        if (!is_numeric($data['delivery_price']) || $data['delivery_price'] <= 0) {
            throw new Exception("Стоимость доставки должна быть положительным числом");
        }
    }
}