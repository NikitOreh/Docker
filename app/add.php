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
if (!in_array($table, $allowed_tables[$role] ?? [])) {
    echo json_encode(['success' => false, 'error' => 'Нет доступа к таблице']);
    exit;
}

// Валидация и подготовка данных
$fields = array_filter($_POST, function($key) { return $key !== 'table'; }, ARRAY_FILTER_USE_KEY);
$columns = array_keys($fields);
$placeholders = array_fill(0, count($fields), '?');
$values = array_values($fields);

try {
    $sql = "INSERT INTO `$table` (`" . implode('`,`', $columns) . "`) VALUES (" . implode(',', $placeholders) . ")";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?>