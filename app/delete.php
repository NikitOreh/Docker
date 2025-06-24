<?php
session_start();
require __DIR__ . '/db.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Не авторизован']);
    exit;
}

$role = $_SESSION['role'];
$allowed_tables = [
    'admin' => ['client', 'employee', 'master', 'equipment_instance', 'maintenance', 'product', 'shipping', 'supply', 'supplier'],
    'accountant' => ['client', 'maintenance', 'product', 'shipping', 'supply', 'supplier']
];

$table = $_POST['table'] ?? '';
$id = $_POST['id'] ?? '';
if (!in_array($table, $allowed_tables[$role] ?? [])) {
    echo json_encode(['success' => false, 'error' => 'Нет доступа к таблице']);
    exit;
}

try {
    // Определение первичного ключа для каждой таблицы
    $primary_keys = [
        'client' => 'client_id',
        'employee' => 'employee_id',
        'master' => 'master_id',
        'equipment_instance' => 'equipment_instance_code',
        'maintenance' => 'maintenance_number',
        'product' => 'product_code',
        'shipping' => 'shipment_number',
        'supply' => 'supply_number',
        'supplier' => 'supplier_id'
    ];

    $primary_key = $primary_keys[$table] ?? '';
    if (!$primary_key) {
        echo json_encode(['success' => false, 'error' => 'Неизвестная таблица']);
        exit;
    }

    $sql = "DELETE FROM `$table` WHERE `$primary_key` = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?>