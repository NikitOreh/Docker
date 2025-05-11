<?php
require_once __DIR__ . '/../src/core/routers.php';
require_once __DIR__ . '/../app/core/Database.php';

session_start();

// Инициализация роутера
$router = new Router();

// Маршруты
$router->add('GET', '/', 'HomeController@index');
$router->add('GET', '/deliveries', 'DeliveryController@index');
$router->add('GET', '/deliveries/create', 'DeliveryController@create');
$router->add('POST', '/deliveries/store', 'DeliveryController@store');
$router->add('GET', '/deliveries/show/{id}', 'DeliveryController@show');
$router->add('POST', '/import/csv', 'ImportController@handleImport');

// Обработка запроса
try {
    $router->dispatch();
} catch (Exception $e) {
    // Логирование ошибки
    error_log($e->getMessage());
    
    // Показ страницы ошибки
    http_response_code(500);
    if (file_exists(__DIR__ . '/../app/views/errors/500.php')) {
        require __DIR__ . '/../app/views/errors/500.php';
    } else {
        echo "Произошла ошибка. Пожалуйста, попробуйте позже.";
    }
    exit();
}