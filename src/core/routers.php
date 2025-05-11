<?php
use App\core\Router;

$router = new Router();

 // Главная страница
 $router->add('GET', '/', 'HomeController@index');


 // Доставки
 $router->add('GET', '/deliveries', 'DeliveryController@index');
 $router->add('GET', '/deliveries/create', 'DeliveryController@create');
 $router->add('POST', '/deliveries', 'DeliveryController@store');
 $router->add('GET', '/deliveries/{id}', 'DeliveryController@show');
 $router->add('GET', '/deliveries/{id}/edit', 'DeliveryController@edit');
 $router->add('POST', '/deliveries/{id}', 'DeliveryController@update');
 $router->add('POST', '/deliveries/{id}/delete', 'DeliveryController@destroy');

// Заказы
$router->get('/orders', 'OrderController@index');
$router->get('/orders/create', 'OrderController@create');
$router->post('/orders', 'OrderController@store');
$router->get('/orders/{id}', 'OrderController@show');

// Импорт
$router->get('/import', 'ImportController@index');
$router->post('/import/csv', 'ImportController@handleImport');

return $router;