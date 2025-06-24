<?php
header('Content-Type: application/json');
session_start();
require 'db.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Не авторизован']);
    exit;
}

$role = $_SESSION['role'];

// Разрешенные таблицы для каждой роли
$allowed_tables = [
    'admin' => ['client', 'employee', 'master', 'equipment_instance', 'maintenance', 'product', 'shipping', 'supply', 'supplier', 'shipment_product'],
    'accountant' => ['client', 'employee', 'maintenance', 'product', 'shipping', 'supply', 'supplier', 'shipment_product'],
    'employee' => ['product', 'equipment_instance', 'shipping', 'maintenance'],
    'master' => ['equipment_instance', 'maintenance']
];

try {
    if (!isset($_POST['table'])) {
        echo json_encode(['success' => false, 'error' => 'Не указана таблица']);
        exit;
    }

    $table = $_POST['table'];

    // Проверка доступа к таблице
    if (!in_array($table, $allowed_tables[$role])) {
        echo json_encode(['success' => false, 'error' => 'Доступ к этой таблице запрещен']);
        exit;
    }

    error_log('update.php: Received POST data for ' . $table . ': ' . print_r($_POST, true));

    switch ($table) {
        case 'client':
            if (!isset($_POST['id'], $_POST['client_email'], $_POST['client_phone'], $_POST['client_address'], $_POST['client_company_or_full_name'])) {
                echo json_encode(['success' => false, 'error' => 'Недостаточно данных для client']);
                exit;
            }
            $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
            $client_email = $_POST['client_email'] ?: null;
            $client_phone = filter_var($_POST['client_phone'], FILTER_VALIDATE_INT);
            $client_address = $_POST['client_address'];
            $client_company_or_full_name = $_POST['client_company_or_full_name'];
            if (!$id || !$client_phone) {
                echo json_encode(['success' => false, 'error' => 'Некорректный ID или номер телефона']);
                exit;
            }
            $stmt = $pdo->prepare('UPDATE client SET client_email = ?, client_phone = ?, client_address = ?, client_company_or_full_name = ? WHERE client_id = ?');
            $stmt->execute([$client_email, $client_phone, $client_address, $client_company_or_full_name, $id]);
            break;

        case 'employee':
            if (!isset($_POST['id'], $_POST['employee_full_name'], $_POST['employee_phone'])) {
                echo json_encode(['success' => false, 'error' => 'Недостаточно данных для employee']);
                exit;
            }
            $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
            $employee_full_name = $_POST['employee_full_name'];
            $employee_phone = filter_var($_POST['employee_phone'], FILTER_VALIDATE_INT);
            $shipment_number = isset($_POST['shipment_number']) ? filter_var($_POST['shipment_number'], FILTER_VALIDATE_INT) : null;
            if (!$id || !$employee_phone) {
                echo json_encode(['success' => false, 'error' => 'Некорректный ID или номер телефона']);
                exit;
            }
            $stmt = $pdo->prepare('UPDATE employee SET employee_full_name = ?, employee_phone = ?, shipment_number = ? WHERE employee_id = ?');
            $stmt->execute([$employee_full_name, $employee_phone, $shipment_number, $id]);
            break;

        case 'master':
            if (!isset($_POST['id'], $_POST['master_full_name'], $_POST['master_phone'], $_POST['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Недостаточно данных для master']);
                exit;
            }
            $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
            $master_full_name = $_POST['master_full_name'];
            $master_phone = filter_var($_POST['master_phone'], FILTER_VALIDATE_INT);
            $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
            $maintenance_number = isset($_POST['maintenance_number']) ? filter_var($_POST['maintenance_number'], FILTER_VALIDATE_INT) : null;
            if (!$id || !$master_phone || !$user_id) {
                echo json_encode(['success' => false, 'error' => 'Некорректный ID, номер телефона или ID пользователя']);
                exit;
            }
            $stmt = $pdo->prepare('UPDATE master SET master_full_name = ?, master_phone = ?, user_id = ?, maintenance_number = ? WHERE master_id = ?');
            $stmt->execute([$master_full_name, $master_phone, $user_id, $maintenance_number, $id]);
            break;

        case 'equipment_instance':
            if (!isset($_POST['id'], $_POST['equipment_instance_name'], $_POST['employee_id'], $_POST['product_code'], $_POST['equipment_instance_status'], $_POST['equipment_instance_price'])) {
                echo json_encode(['success' => false, 'error' => 'Недостаточно данных для equipment_instance']);
                exit;
            }
            $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
            $equipment_instance_name = $_POST['equipment_instance_name'];
            $employee_id = filter_var($_POST['employee_id'], FILTER_VALIDATE_INT) ?: null;
            $product_code = filter_var($_POST['product_code'], FILTER_VALIDATE_INT);
            $equipment_instance_status = $_POST['equipment_instance_status'];
            $equipment_instance_price = filter_var($_POST['equipment_instance_price'], FILTER_VALIDATE_FLOAT);
            if (!$id || !$product_code || !$equipment_instance_price) {
                echo json_encode(['success' => false, 'error' => 'Некорректный ID, код продукта или цена']);
                exit;
            }
            $stmt = $pdo->prepare('UPDATE equipment_instance SET equipment_instance_name = ?, employee_id = ?, product_code = ?, equipment_instance_status = ?, equipment_instance_price = ? WHERE equipment_instance_code = ?');
            $stmt->execute([$equipment_instance_name, $employee_id, $product_code, $equipment_instance_status, $equipment_instance_price, $id]);
            break;

        case 'maintenance':
            if (!isset($_POST['equipment_instance_code'], $_POST['maintenance_number'], $_POST['master_id'], $_POST['maintenance_price'], $_POST['maintenance_status'], $_POST['maintenance_date'])) {
                echo json_encode(['success' => false, 'error' => 'Недостаточно данных для maintenance']);
                exit;
            }
            $equipment_instance_code = filter_var($_POST['equipment_instance_code'], FILTER_VALIDATE_INT);
            $maintenance_number = filter_var($_POST['maintenance_number'], FILTER_VALIDATE_INT);
            $master_id = filter_var($_POST['master_id'], FILTER_VALIDATE_INT);
            $maintenance_price = filter_var($_POST['maintenance_price'], FILTER_VALIDATE_FLOAT);
            $maintenance_status = $_POST['maintenance_status'];
            $maintenance_date = $_POST['maintenance_date'];
            $master_full_name = isset($_POST['master_full_name']) ? $_POST['master_full_name'] : null;
            $is_new = isset($_POST['is_new']) ? filter_var($_POST['is_new'], FILTER_VALIDATE_INT) : 0;
            if (!$equipment_instance_code || !$maintenance_number || !$master_id || !$maintenance_price) {
                echo json_encode(['success' => false, 'error' => 'Некорректные данные для maintenance']);
                exit;
            }
            $stmt = $pdo->prepare('UPDATE maintenance SET master_id = ?, maintenance_price = ?, maintenance_status = ?, maintenance_date = ?, master_full_name = ?, is_new = ? WHERE equipment_instance_code = ? AND maintenance_number = ?');
            $stmt->execute([$master_id, $maintenance_price, $maintenance_status, $maintenance_date, $master_full_name, $is_new, $equipment_instance_code, $maintenance_number]);
            break;

        case 'product':
            if (!isset($_POST['id'], $_POST['product_name'], $_POST['price'], $_POST['stock_quantity'], $_POST['product_unit_of_measurement'])) {
                echo json_encode(['success' => false, 'error' => 'Недостаточно данных для product']);
                exit;
            }
            $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
            $product_name = $_POST['product_name'];
            $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
            $stock_quantity = filter_var($_POST['stock_quantity'], FILTER_VALIDATE_INT);
            $product_unit_of_measurement = $_POST['product_unit_of_measurement'];
            if (!$id || !$price || !$stock_quantity) {
                echo json_encode(['success' => false, 'error' => 'Некорректный ID, цена или количество']);
                exit;
            }
            $stmt = $pdo->prepare('UPDATE product SET product_name = ?, price = ?, stock_quantity = ?, product_unit_of_measurement = ? WHERE product_code = ?');
            $stmt->execute([$product_name, $price, $stock_quantity, $product_unit_of_measurement, $id]);
            break;

        case 'shipping':
            if (!isset($_POST['id'], $_POST['shipping_date'], $_POST['shipment_status'], $_POST['employee_id'], $_POST['client_id'])) {
                echo json_encode(['success' => false, 'error' => 'Недостаточно данных для shipping']);
                exit;
            }
            $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
            $shipping_date = $_POST['shipping_date'];
            $shipment_status = $_POST['shipment_status'];
            $employee_id = filter_var($_POST['employee_id'], FILTER_VALIDATE_INT);
            $client_id = filter_var($_POST['client_id'], FILTER_VALIDATE_INT);
            $employee_full_name = isset($_POST['employee_full_name']) ? $_POST['employee_full_name'] : null;
            $is_new = isset($_POST['is_new']) ? filter_var($_POST['is_new'], FILTER_VALIDATE_INT) : 1;
            if (!$id || !$employee_id || !$client_id) {
                echo json_encode(['success' => false, 'error' => 'Некорректный ID, ID сотрудника или клиента']);
                exit;
            }
            $stmt = $pdo->prepare('UPDATE shipping SET shipping_date = ?, shipment_status = ?, employee_id = ?, client_id = ?, employee_full_name = ?, is_new = ? WHERE shipment_number = ?');
            $stmt->execute([$shipping_date, $shipment_status, $employee_id, $client_id, $employee_full_name, $is_new, $id]);
            break;

        case 'supply':
            if (!isset($_POST['id'], $_POST['supply_status'], $_POST['supplier_id'])) {
                echo json_encode(['success' => false, 'error' => 'Недостаточно данных для supply']);
                exit;
            }
            $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
            $supply_status = $_POST['supply_status'];
            $supplier_id = filter_var($_POST['supplier_id'], FILTER_VALIDATE_INT);
            if (!$id || !$supplier_id) {
                echo json_encode(['success' => false, 'error' => 'Некорректный ID или ID поставщика']);
                exit;
            }
            $stmt = $pdo->prepare('UPDATE supply SET supply_status = ?, supplier_id = ? WHERE supply_number = ?');
            $stmt->execute([$supply_status, $supplier_id, $id]);
            break;

        case 'supplier':
            if (!isset($_POST['id'], $_POST['supplier_company_or_full_name'], $_POST['supplier_email'], $_POST['supplier_phone'], $_POST['supplier_address'])) {
                echo json_encode(['success' => false, 'error' => 'Недостаточно данных для supplier']);
                exit;
            }
            $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
            $supplier_company_or_full_name = $_POST['supplier_company_or_full_name'];
            $supplier_email = $_POST['supplier_email'] ?: null;
            $supplier_phone = filter_var($_POST['supplier_phone'], FILTER_VALIDATE_INT);
            $supplier_address = $_POST['supplier_address'];
            $supplier_full_name = isset($_POST['supplier_full_name']) ? $_POST['supplier_full_name'] : '';
            if (!$id || !$supplier_phone) {
                echo json_encode(['success' => false, 'error' => 'Некорректный ID или номер телефона']);
                exit;
            }
            $stmt = $pdo->prepare('UPDATE supplier SET supplier_company_or_full_name = ?, supplier_email = ?, supplier_phone = ?, supplier_address = ?, supplier_full_name = ? WHERE supplier_id = ?');
            $stmt->execute([$supplier_company_or_full_name, $supplier_email, $supplier_phone, $supplier_address, $supplier_full_name, $id]);
            break;

        case 'shipment_product':
            if (!isset($_POST['shipment_number'], $_POST['product_code'], $_POST['shipment_product_quantity'], $_POST['shipment_product_price'])) {
                echo json_encode(['success' => false, 'error' => 'Недостаточно данных для shipment_product']);
                exit;
            }
            $shipment_number = filter_var($_POST['shipment_number'], FILTER_VALIDATE_INT);
            $product_code = filter_var($_POST['product_code'], FILTER_VALIDATE_INT);
            $shipment_product_quantity = filter_var($_POST['shipment_product_quantity'], FILTER_VALIDATE_INT);
            $shipment_product_price = filter_var($_POST['shipment_product_price'], FILTER_VALIDATE_FLOAT);
            if (!$shipment_number || !$product_code || !$shipment_product_quantity || !$shipment_product_price) {
                echo json_encode(['success' => false, 'error' => 'Некорректные данные для shipment_product']);
                exit;
            }
            $stmt = $pdo->prepare('UPDATE shipment_product SET shipment_product_quantity = ?, shipment_product_price = ? WHERE shipment_number = ? AND product_code = ?');
            $stmt->execute([$shipment_product_quantity, $shipment_product_price, $shipment_number, $product_code]);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Неизвестная таблица']);
            exit;
    }

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log('update.php: Database error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?>