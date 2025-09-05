<?php
session_start();
require __DIR__ . '/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Неавторизованный доступ']);
    exit;
}

$role = $_SESSION['role'];
$allowed_tables = [
    'admin' => ['client', 'employee', 'master', 'equipment_instance', 'maintenance', 'product', 'shipping', 'supply', 'supplier', 'shipment_product'],
    'accountant' => ['client', 'employee', 'maintenance', 'product', 'shipping', 'supply', 'supplier', 'shipment_product'],
    'employee' => ['product', 'equipment_instance', 'shipping', 'maintenance'],
    'master' => ['equipment_instance', 'maintenance']
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Метод не поддерживается']);
    exit;
}

$table = $_POST['table'] ?? '';
if (!in_array($table, $allowed_tables[$role])) {
    echo json_encode(['error' => 'Доступ к таблице запрещен']);
    exit;
}

$data = $_POST;
unset($data['table']);

// Проверка уникальности идентификаторов
try {
    switch ($table) {
        case 'client':
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM client WHERE client_id = ? AND is_deleted = 0');
            $stmt->execute([$data['client_id']]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['error' => 'Клиент с таким ID уже существует']);
                exit;
            }
            break;
        case 'employee':
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM employee WHERE employee_id = ? AND is_deleted = 0');
            $stmt->execute([$data['employee_id']]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['error' => 'Сотрудник с таким ID уже существует']);
                exit;
            }
            break;
        case 'master':
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM master WHERE master_id = ? AND is_deleted = 0');
            $stmt->execute([$data['master_id']]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['error' => 'Мастер с таким ID уже существует']);
                exit;
            }
            break;
        case 'equipment_instance':
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM equipment_instance WHERE equipment_instance_code = ? AND is_deleted = 0');
            $stmt->execute([$data['equipment_instance_code']]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['error' => 'Оборудование с таким кодом уже существует']);
                exit;
            }
            break;
        case 'maintenance':
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM maintenance WHERE equipment_instance_code = ? AND maintenance_number = ? AND is_deleted = 0');
            $stmt->execute([$data['equipment_instance_code'], $data['maintenance_number']]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['error' => 'Техобслуживание с таким кодом оборудования и номером уже существует']);
                exit;
            }
            break;
        case 'product':
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM product WHERE product_code = ? AND is_deleted = 0');
            $stmt->execute([$data['product_code']]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['error' => 'Товар с таким кодом уже существует']);
                exit;
            }
            break;
        case 'shipping':
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM shipping WHERE shipment_number = ? AND is_deleted = 0');
            $stmt->execute([$data['shipment_number']]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['error' => 'Отгрузка с таким номером уже существует']);
                exit;
            }
            break;
        case 'supply':
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM supply WHERE supply_number = ? AND is_deleted = 0');
            $stmt->execute([$data['supply_number']]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['error' => 'Поставка с таким номером уже существует']);
                exit;
            }
            break;
        case 'supplier':
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM supplier WHERE supplier_id = ? AND is_deleted = 0');
            $stmt->execute([$data['supplier_id']]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['error' => 'Поставщик с таким ID уже существует']);
                exit;
            }
            break;
        case 'shipment_product':
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM shipment_product WHERE shipment_number = ? AND product_code = ?');
            $stmt->execute([$data['shipment_number'], $data['product_code']]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['error' => 'Запись с таким номером отгрузки и кодом товара уже существует']);
                exit;
            }
            break;
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Ошибка проверки уникальности: ' . $e->getMessage()]);
    exit;
}

try {
    $columns = array_keys($data);
    $placeholders = array_fill(0, count($columns), '?');
    $values = array_values($data);

    // Добавление is_deleted
    $columns[] = 'is_deleted';
    $placeholders[] = '?';
    $values[] = 0;

    $query = "INSERT INTO `$table` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
    $stmt = $pdo->prepare($query);
    $stmt->execute($values);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?>