<?php

namespace App\controllers;

use App\models\DeliveryModel;
use App\models\ClientModel;
use App\models\CourierModel;
use App\models\ProductModel;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class DeliveryController
{
    private DeliveryModel $deliveryModel;
    private ClientModel $clientModel;
    private CourierModel $courierModel;
    private ProductModel $productModel;
    private Environment $twig;

    public function __construct()
    {
        $this->deliveryModel = new DeliveryModel();
        $this->clientModel = new ClientModel();
        $this->courierModel = new CourierModel();
        $this->productModel = new ProductModel();

        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader);
    }

    /**
     * Отображение списка всех доставок
     */
    public function index(): void
    {
        $deliveries = $this->deliveryModel->getAllDeliveries();
        $couriers = $this->courierModel->getAll();
        $products = $this->productModel->getAll();

        echo $this->twig->render('deliveries/index.twig', [
            'deliveries' => $deliveries,
            'couriers' => $couriers,
            'products' => $products,
            'styles' => '/assets/css/style.css',
            'scripts' => [
                '/assets/js/phoneinput.js',
                '/assets/js/index.js'
            ]
        ]);
    }

    /**
     * Создание новой доставки
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit();
        }

        try {
            // Создание клиента
            $clientId = $this->clientModel->create(
                trim($_POST['client_name']),
                preg_replace('/[^0-9+]/', '', $_POST['client_phone']),
                trim($_POST['client_mail'] ?? '')
            );

            // Создание курьера
            $courierId = $this->courierModel->create(
                trim($_POST['courier_name'])
            );

            // Создание продукта
            $productId = $this->productModel->create(
                trim($_POST['product_name']),
                (float)$_POST['product_price']
            );

            // Создание доставки
            $deliveryId = $this->deliveryModel->create([
                'client_id' => $clientId,
                'courier_id' => $courierId,
                'delivery_city' => trim($_POST['city']),
                'delivery_street' => trim($_POST['street']),
                'delivery_house' => trim($_POST['house']),
                'delivery_entrance' => !empty($_POST['entrance']) ? trim($_POST['entrance']) : null,
                'delivery_apartment' => !empty($_POST['apartment']) ? trim($_POST['apartment']) : null,
                'delivery_floor' => !empty($_POST['floor']) ? trim($_POST['floor']) : null,
                'delivery_intercome_code' => !empty($_POST['intercom_code']) ? trim($_POST['intercom_code']) : null,
                'delivery_date' => date('Y-m-d', strtotime($_POST['delivery_date'])),
                'delivery_price' => (float)$_POST['delivery_price'],
                'delivery_tip' => !empty($_POST['delivery_tip']) ? (float)$_POST['delivery_tip'] : 0,
                'delivery_type' => trim($_POST['delivery_type'])
            ]);

            // Связывание продукта с доставкой
            $this->deliveryModel->addProductToDelivery($productId, $deliveryId);

            $_SESSION['success'] = 'Доставка успешно создана! Номер: ' . $deliveryId;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Ошибка при создании доставки: ' . $e->getMessage();
            $_SESSION['old_input'] = $_POST;
        }

        header('Location: /deliveries');
    }

    /**
     * Просмотр деталей доставки
     */
    public function show(int $id): void
    {
        $delivery = $this->deliveryModel->getById($id);
        
        if (!$delivery) {
            header('HTTP/1.1 404 Not Found');
            echo $this->twig->render('errors/404.twig');
            return;
        }

        $delivery['products'] = $this->productModel->getByDeliveryId($id);
        $delivery['client'] = $this->clientModel->getById($delivery['client_id']);
        $delivery['courier'] = $this->courierModel->getById($delivery['courier_id']);

        echo $this->twig->render('deliveries/show.twig', [
            'delivery' => $delivery,
            'styles' => '/assets/css/style.css'
        ]);
    }
}