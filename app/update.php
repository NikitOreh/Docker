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

// Получение данных из JSON
$input = json_decode(file_get_contents('php://input'), true);
error_log('update.php: Received data: ' . print_r($input, true));

if (!isset($input['table'])) {
    echo json_encode(['success' => false, 'error' => 'Не указана таблица']);
    exit;
}

$table = $input['table'];

// Проверка доступа к таблице
if (!in_array($table, $allowed_tables[$role])) {
    echo json_encode(['success' => false, 'error' => 'Доступ к этой таблице запрещен']);
    exit;
}

try {
    // Определение ключевых полей и их типов для каждой таблицы
    $key_fields = [
        'client' => ['client_id' => 'int'],
        'employee' => ['employee_id' => 'int'],
        'master' => ['master_id' => 'int'],
        'equipment_instance' => ['equipment_instance_code' => 'int'],
        'maintenance' => ['equipment_instance_name' => 'string', 'maintenance_number' => 'int'],
        'product' => ['product_code' => 'int'],
        'shipping' => ['shipment_number' => 'int'],
        'supply' => ['supply_number' => 'int'],
        'supplier' => ['supplier_id' => 'int'],
        'shipment_product' => ['shipment_number' => 'int', 'product_code' => 'int']
    ];

    // Проверка наличия ключевых полей
    if (!isset($key_fields[$table])) {
        echo json_encode(['success' => false, 'error' => 'Неизвестная таблица']);
        exit;
    }

    $keys = $key_fields[$table];
    $key_values = [];
    foreach ($keys as $key => $type) {
        if (!isset($input[$key])) {
            echo json_encode(['success' => false, 'error' => "Отсутствует ключевое поле: $key"]);
            exit;
        }
        $key_values[$key] = $type === 'int' ? filter_var($input[$key], FILTER_VALIDATE_INT) : $input[$key];
        if ($key_values[$key] === false && $type === 'int') {
            echo json_encode(['success' => false, 'error' => "Некорректное значение для $key"]);
            exit;
        }
    }

    // Поля для обновления (исключаем table и ключевые поля)
    $fields = array_diff_key($input, array_fill_keys(array_keys($keys), true), ['table' => true]);
    if (empty($fields)) {
        echo json_encode(['success' => false, 'error' => 'Нет данных для обновления']);
        exit;
    }

    // Специальная обработка для таблиц с внешними ключами
    if ($table === 'maintenance') {
        if (isset($fields['master_full_name'])) {
            $stmt = $pdo->prepare('SELECT master_id FROM master WHERE master_full_name = ?');
            $stmt->execute([$fields['master_full_name']]);
            $fields['master_id'] = $stmt->fetchColumn();
            if (!$fields['master_id']) {
                echo json_encode(['success' => false, 'error' => 'Мастер не найден']);
                exit;
            }
        }
        if (isset($fields['equipment_instance_name'])) {
            $stmt = $pdo->prepare('SELECT equipment_instance_code FROM equipment_instance WHERE equipment_instance_name = ?');
            $stmt->execute([$fields['equipment_instance_name']]);
            $fields['equipment_instance_code'] = $stmt->fetchColumn();
            if (!$fields['equipment_instance_code']) {
                echo json_encode(['success' => false, 'error' => 'Оборудование не найдено']);
                exit;
            }
        }
    } elseif ($table === 'shipping') {
        if (isset($fields['employee_full_name'])) {
            $stmt = $pdo->prepare('SELECT employee_id FROM employee WHERE employee_full_name = ?');
            $stmt->execute([$fields['employee_full_name']]);
            $fields['employee_id'] = $stmt->fetchColumn();
            if (!$fields['employee_id']) {
                echo json_encode(['success' => false, 'error' => 'Сотрудник не найден']);
                exit;
            }
        }
        if (isset($fields['client_company_or_full_name'])) {
            $stmt = $pdo->prepare('SELECT client_id FROM client WHERE client_company_or_full_name = ?');
            $stmt->execute([$fields['client_company_or_full_name']]);
            $fields['client_id'] = $stmt->fetchColumn();
            if (!$fields['client_id']) {
                echo json_encode(['success' => false, 'error' => 'Клиент не найден']);
                exit;
            }
        }
    } elseif ($table === 'supply') {
        if (isset($fields['supplier_company_or_full_name'])) {
            $stmt = $pdo->prepare('SELECT supplier_id FROM supplier WHERE supplier_company_or_full_name = ?');
            $stmt->execute([$fields['supplier_company_or_full_name']]);
            $fields['supplier_id'] = $stmt->fetchColumn();
            if (!$fields['supplier_id']) {
                echo json_encode(['success' => false, 'error' => 'Поставщик не найден']);
                exit;
            }
        }
    }

    // Валидация числовых полей
    $numeric_fields = [
        'maintenance_price' => 'float',
        'equipment_instance_price' => 'float',
        'price' => 'float',
        'stock_quantity' => 'int',
        'shipment_product_quantity' => 'int',
        'shipment_product_price' => 'float'
    ];
    foreach ($fields as $field => $value) {
        if (isset($numeric_fields[$field])) {
            $fields[$field] = filter_var($value, $numeric_fields[$field] === 'int' ? FILTER_VALIDATE_INT : FILTER_VALIDATE_FLOAT);
            if ($fields[$field] === false) {
                echo json_encode(['success' => false, 'error' => "Некорректное значение для $field"]);
                exit;
            }
        }
    }

    // Формирование SQL-запроса
    $set_clause = implode(', ', array_map(fn($key) => "$key = ?", array_keys($fields)));
    $where_clause = implode(' AND ', array_map(fn($key) => "$key = ?", array_keys($keys)));
    $stmt = $pdo->prepare("UPDATE $table SET $set_clause WHERE $where_clause");
    $values = array_values($fields);
    $values = array_merge($values, array_values($key_values));
    $stmt->execute($values);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log('update.php: Database error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?>