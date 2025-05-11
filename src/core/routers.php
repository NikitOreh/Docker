<?php
require_once __DIR__ . '/router.php';

$router = new Router();

$router->get('/', function() {
    echo "<h1>Главная</h1>";
});

$router->get('/import', function() {
    echo "<h1>Импорт</h1>";
});

$router->post('/submit', function() {
    echo "Форма отправлена";
});

$router->resolve();
