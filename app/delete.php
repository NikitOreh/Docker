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
    'admin' => ['client', 'employee', 'master', 'equipment_instance', 'maintenance', 'product', 'shipping', 'supply', 'supplier', 'shipment_product'],
    'accountant' => ['client', 'employee', 'maintenance', 'product', 'shipping', 'supply', 'supplier', 'shipment_product'],
    'employee' => ['product', 'equipment_instance', 'shipping', 'maintenance'],
    'master' => ['equipment_instance', 'maintenance']
];

$table = $_POST['table'] ?? '';
if (!in_array($table, $allowed_tables[$role] ?? [])) {
    echo json_encode(['success' => false, 'error' => 'Нет доступа к таблице']);
    exit;
}

try {
    // Определение первичных ключей для каждой таблицы
    $primary_keys = [
        'client' => 'client_id',
        'employee' => 'employee_id',
        'master' => 'master_id',
        'equipment_instance' => 'equipment_instance_code',
        'maintenance' => 'maintenance_number',
        'product' => 'product_code',
        'shipping' => 'shipment_number',
        'supply' => 'supply_number',
        'supplier' => 'supplier_id',
        'shipment_product' => ['shipment_number', 'product_code'] // составной ключ
    ];

    $primary_key = $primary_keys[$table] ?? '';
    if (!$primary_key) {
        echo json_encode(['success' => false, 'error' => 'Неизвестная таблица']);
        exit;
    }

    if (is_array($primary_key)) {
        // Для таблиц с составными ключами (например, shipment_product)
        $where_conditions = [];
        $values = [];
        foreach ($primary_key as $key) {
            if (!isset($_POST[$key])) {
                echo json_encode(['success' => false, 'error' => "Отсутствует значение для $key"]);
                exit;
            }
            $where_conditions[] = "$key = ?";
            $values[] = $_POST[$key];
        }
        $where = implode(' AND ', $where_conditions);
    } else {
        // Для таблиц с одним первичным ключом
        $id = $_POST['id'] ?? '';
        if (empty($id)) {
            echo json_encode(['success' => false, 'error' => 'Отсутствует ID']);
            exit;
        }
        $where = "$primary_key = ?";
        $values = [$id];
    }

    // Мягкое удаление: устанавливаем is_deleted = 1
    $sql = "UPDATE `$table` SET is_deleted = 1 WHERE $where";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?>