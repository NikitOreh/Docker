<?php

namespace App\controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\models\DeliveryModel;
use App\models\ProductModel;

class HomeController
{
    private Environment $twig;
    private DeliveryModel $deliveryModel;
    private ProductModel $productModel;

    public function __construct()
    {
        // Инициализация Twig
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader);
        
        // Инициализация моделей
        $this->deliveryModel = new DeliveryModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Отображение главной страницы
     */
    public function index(): void
    {
        try {
            // Получаем последние 5 доставок
            $recentDeliveries = $this->deliveryModel->getRecentDeliveries(5);
            
            // Получаем популярные товары
            $popularProducts = $this->productModel->getPopularProducts(3);
            
            // Статистика доставок
            $stats = [
                'today' => $this->deliveryModel->getDeliveryCountByDate(date('Y-m-d')),
                'month' => $this->deliveryModel->getDeliveryCountByDateRange(
                    date('Y-m-01'),
                    date('Y-m-t')
                ),
                'total' => $this->deliveryModel->getCount()
            ];

            echo $this->twig->render('home/index.twig', [
                'recentDeliveries' => $recentDeliveries,
                'popularProducts' => $popularProducts,
                'stats' => $stats,
                'styles' => '/assets/css/style.css',
                'scripts' => [
                    '/assets/js/main.js',
                    '/assets/js/charts.js'
                ]
            ]);
            
        } catch (\Exception $e) {
            // Логирование ошибки
            error_log('HomeController error: ' . $e->getMessage());
            
            // Показ страницы с ошибкой
            echo $this->twig->render('errors/500.twig', [
                'message' => 'Произошла ошибка при загрузке данных'
            ]);
        }
    }

    /**
     * Отображение страницы "О нас"
     */
    public function about(): void
    {
        echo $this->twig->render('home/about.twig', [
            'styles' => '/assets/css/style.css'
        ]);
    }
}