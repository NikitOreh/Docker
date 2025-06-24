<?php
session_start();
require __DIR__ . '/db.php';
// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];
$employee_id = $_SESSION['employee_id'] ?? null;
$master_id = $_SESSION['master_id'] ?? null;
$errors = [];
$success = '';

// Определение доступных таблиц для каждой роли
$allowed_tables = [
    'admin' => ['client', 'employee', 'master', 'equipment_instance', 'maintenance', 'product', 'shipping', 'supply', 'supplier'],
    'accountant' => ['client', 'employee', 'maintenance', 'product', 'shipping', 'supply', 'supplier'],
    'employee' => ['product', 'equipment_instance', 'shipping', 'maintenance'],
    'master' => ['equipment_instance', 'maintenance']
];

// Обработка параметров сортировки
$sort_field = $_GET['sort'] ?? '';
$sort_direction = isset($_GET['direction']) && in_array(strtoupper($_GET['direction']), ['ASC', 'DESC']) ? strtoupper($_GET['direction']) : 'ASC';
$sort_table = $_GET['table'] ?? '';

// Обработка добавления отгрузки для бухгалтера
if ($role === 'accountant' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_shipping'])) {
    $shipment_number = trim($_POST['shipment_number'] ?? '');
    $shipping_date = trim($_POST['shipping_date'] ?? '');
    $shipment_status = trim($_POST['shipment_status'] ?? '');
    $employee_id_post = trim($_POST['employee_full_name'] ?? '');
    $client_id = trim($_POST['client_company_or_full_name'] ?? '');

    if (empty($shipment_number)) $errors[] = 'Номер отгрузки обязателен.';
    if (empty($shipping_date)) $errors[] = 'Дата отгрузки обязательна.';
    if (empty($shipment_status)) $errors[] = 'Статус отгрузки обязателен.';
    if (empty($employee_id_post)) $errors[] = 'Сотрудник обязателен.';
    if (empty($client_id)) $errors[] = 'Клиент обязателен.';

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('INSERT INTO shipping (shipment_number, shipping_date, shipment_status, employee_full_name, client_company_or_full_name, is_new) VALUES (?, ?, ?, ?, ?, 1)');
            $stmt->execute([$shipment_number, $shipping_date, $shipment_status, $employee_id_post, $client_id]);
            $success = 'Отгрузка успешно добавлена.';
        } catch (PDOException $e) {
            $errors[] = 'Ошибка добавления отгрузки: ' . $e->getMessage();
        }
    }
}

// Обработка добавления техобслуживания для сотрудника
if ($role === 'employee' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_maintenance'])) {
    $equipment_instance_code = trim($_POST['equipment_instance_name'] ?? '');
    $maintenance_number = trim($_POST['maintenance_number'] ?? '');
    $master_id_post = trim($_POST['master_full_name'] ?? '');
    $maintenance_price = trim($_POST['maintenance_price'] ?? '');
    $maintenance_status = trim($_POST['maintenance_status'] ?? '');
    $maintenance_date = trim($_POST['maintenance_date'] ?? '');

    if (empty($equipment_instance_code)) $errors[] = 'Код оборудования обязателен.';
    if (empty($maintenance_number)) $errors[] = 'Номер обслуживания обязателен.';
    if (empty($master_id_post)) $errors[] = 'Мастер обязателен.';
    if (empty($maintenance_price) || !is_numeric($maintenance_price) || $maintenance_price < 0) $errors[] = 'Укажите корректную цену обслуживания.';
    if (empty($maintenance_status)) $errors[] = 'Статус обслуживания обязателен.';
    if (empty($maintenance_date)) $errors[] = 'Дата обслуживания обязательна.';

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('INSERT INTO maintenance (equipment_instance_name, maintenance_number, master_full_name, maintenance_price, maintenance_status, maintenance_date, is_new) VALUES (?, ?, ?, ?, ?, ?, 1)');
            $stmt->execute([$equipment_instance_code, $maintenance_number, $master_id_post, $maintenance_price, $maintenance_status, $maintenance_date]);
            $success = 'Техобслуживание успешно добавлено.';
        } catch (PDOException $e) {
            $errors[] = 'Ошибка добавления техобслуживания: ' . $e->getMessage();
        }
    }
}

// Получение списка сотрудников и клиентов для формы
if ($role === 'accountant') {
    try {
        $stmt = $pdo->query('SELECT employee_id, employee_full_name FROM employee ORDER BY employee_full_name');
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = $pdo->query('SELECT client_id, client_company_or_full_name FROM client ORDER BY client_company_or_full_name');
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $errors[] = 'Ошибка загрузки данных: ' . $e->getMessage();
    }
}

// Получение списка оборудования и мастеров для формы сотрудника
if ($role === 'employee') {
    try {
        $stmt = $pdo->query('SELECT equipment_instance_code, equipment_instance_name FROM equipment_instance ORDER BY equipment_instance_name');
        $equipment_instances = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = $pdo->query("SHOW TABLES LIKE 'master'");
        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->query('SELECT m.master_id, m.master_full_name FROM master m JOIN users u ON m.user_id = u.user_id WHERE u.role = "master" ORDER BY m.master_full_name');
            $masters = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $errors[] = 'Таблица master не найдена в базе данных.';
            $masters = [];
        }
    } catch (PDOException $e) {
        $errors[] = 'Ошибка загрузки данных оборудования или мастеров: ' . $e->getMessage();
    }
}

// Проверка новых отгрузок для сотрудника
$new_shipments = [];
if ($role === 'employee' && $employee_id) {
    try {
        $stmt = $pdo->prepare('SELECT shipment_number, shipping_date, client_id FROM shipping WHERE employee_id = ? AND is_new = 1');
        $stmt->execute([$employee_id]);
        $new_shipments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($new_shipments)) {
            $stmt = $pdo->prepare('UPDATE shipping SET is_new = 0 WHERE employee_id = ? AND is_new = 1');
            $stmt->execute([$employee_id]);
        }
    } catch (PDOException $e) {
        $errors[] = 'Ошибка загрузки отгрузок: ' . $e->getMessage();
    }
}

// Проверка новых техобслуживаний для мастера
$new_maintenances = [];
if ($role === 'master' && $master_id) {
    try {
        $stmt = $pdo->prepare('SELECT maintenance_number, maintenance_date, equipment_instance_code FROM maintenance WHERE master_id = ? AND is_new = 1');
        $stmt->execute([$master_id]);
        $new_maintenances = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($new_maintenances)) {
            $stmt = $pdo->prepare('UPDATE maintenance SET is_new = 0 WHERE master_id = ? AND is_new = 1');
            $stmt->execute([$master_id]);
        }
    } catch (PDOException $e) {
        $errors[] = 'Ошибка загрузки техобслуживаний: ' . $e->getMessage();
    }
}

// Загрузка данных для таблиц
$data = [];
foreach ($allowed_tables[$role] as $table) {
    try {
        $order_by = '';
        if ($sort_table === $table && !empty($sort_field)) {
            $order_by = " ORDER BY `$sort_field` $sort_direction";
        }
        switch ($table) {
            case 'client':
                $stmt = $pdo->query("SELECT client_id, client_email, client_phone, client_address, client_company_or_full_name FROM client$order_by");
                $data['client'] = ['headers' => ['ID', 'Email', 'Телефон', 'Адрес', 'Компания'], 'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
                break;
            case 'employee':
                $stmt = $pdo->query("SELECT employee_id, employee_full_name, employee_phone FROM employee$order_by");
                $data['employee'] = ['headers' => ['ID', 'ФИО', 'Телефон'], 'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
                break;
            case 'master':
                $stmt = $pdo->query("SHOW TABLES LIKE 'master'");
                if ($stmt->rowCount() > 0) {
                    if ($role === 'master' && $master_id) {
                        $stmt = $pdo->prepare("SELECT master_id, master_full_name, master_phone FROM master WHERE master_id = ?$order_by");
                        $stmt->execute([$master_id]);
                    } else {
                        $stmt = $pdo->query("SELECT master_id, master_full_name, master_phone FROM master$order_by");
                    }
                    $data['master'] = ['headers' => ['ID', 'ФИО', 'Телефон'], 'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
                } else {
                    $errors[] = 'Таблица master не найдена.';
                    $data['master'] = ['headers' => ['ID', 'ФИО', 'Телефон'], 'rows' => []];
                }
                break;
            case 'equipment_instance':
                $stmt = $pdo->query("SELECT equipment_instance_code, equipment_instance_name, employee_full_name, equipment_instance_status, equipment_instance_price FROM equipment_instance$order_by");
                $data['equipment_instance'] = ['headers' => ['Код', 'Название', 'Сотрудник', 'Статус', 'Цена'], 'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
                break;
            case 'maintenance':
                if ($role === 'master' && $master_id) {
                    $stmt = $pdo->prepare("SELECT equipment_instance_name, maintenance_number, master_full_name, maintenance_price, maintenance_status, maintenance_date FROM maintenance WHERE master_id = ?$order_by");
                    $stmt->execute([$master_id]);
                } else {
                    $stmt = $pdo->query("SELECT equipment_instance_name, maintenance_number, master_full_name, maintenance_price, maintenance_status, maintenance_date FROM maintenance$order_by");
                }
                $data['maintenance'] = ['headers' => ['Оборудование', 'Номер обслуживания', 'Мастер', 'Цена', 'Статус', 'Дата'], 'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
                break;
            case 'product':
                $stmt = $pdo->query("SELECT product_code, product_name, price, stock_quantity, product_unit_of_measurement FROM product$order_by");
                $data['product'] = ['headers' => ['Код', 'Название', 'Цена', 'Остаток', 'Единица измерения'], 'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
                break;
            case 'shipping':
                $stmt = $pdo->query("SELECT shipment_number, shipping_date, shipment_status, employee_full_name, client_company_or_full_name FROM shipping$order_by");
                $data['shipping'] = ['headers' => ['Номер отгрузки', 'Дата', 'Статус', 'Сотрудник', 'Клиент'], 'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
                break;
            case 'supply':
                $stmt = $pdo->query("SELECT supply_number, supply_status, supplier_company_or_full_name FROM supply$order_by");
                $data['supply'] = ['headers' => ['Номер поставки', 'Статус', 'Поставщик'], 'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
                break;
            case 'supplier':
                $stmt = $pdo->query("SELECT supplier_id, supplier_company_or_full_name, supplier_email, supplier_phone, supplier_address FROM supplier$order_by");
                $data['supplier'] = ['headers' => ['ID', 'Компания', 'Email', 'Телефон', 'Адрес'], 'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
                break;
        }
    } catch (PDOException $e) {
        $errors[] = "Ошибка при запросе к таблице $table: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление складом</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body data-debug="true">
    <header>
        <h1>Управление складом</h1>
        <p>Пользователь: <?php echo htmlspecialchars($username); ?> (
            <?php
            switch ($role) {
                case 'admin': echo 'Администратор'; break;
                case 'accountant': echo 'Бухгалтер'; break;
                case 'employee': echo 'Сотрудник'; break;
                case 'master': echo 'Мастер'; break;
            }
            ?>
        )</p>
        <a href="report.php?format=pdf" class="btn btn-secondary">Скачать PDF</a>
        <a href="report.php?format=excel" class="btn btn-secondary">Скачать Excel</a>
        <a href="login.php" class="btn btn-danger">Выйти</a>
    </header>

    <main class="container mt-4">
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($role === 'employee' && !empty($new_shipments)): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Новая отгрузка!</strong> У вас есть новые отгрузки:
                <ul>
                    <?php foreach ($new_shipments as $shipment): ?>
                        <li>Отгрузка №<?php echo htmlspecialchars($shipment['shipment_number']); ?> от <?php echo htmlspecialchars($shipment['shipping_date']); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
            </div>
        <?php endif; ?>

        <?php if ($role === 'master' && !empty($new_maintenances)): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Новое техобслуживание!</strong> У вас есть новые задания:
                <ul>
                    <?php foreach ($new_maintenances as $maintenance): ?>
                        <li>Техобслуживание №<?php echo htmlspecialchars($maintenance['maintenance_number']); ?> от <?php echo htmlspecialchars($maintenance['maintenance_date']); ?> (Код оборудования: <?php echo htmlspecialchars($maintenance['equipment_instance_code']); ?>)</li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
            </div>
        <?php endif; ?>

        <?php if ($role === 'accountant'): ?>
            <h3 class="mt-3">Добавить новую отгрузку</h3>
            <form method="POST" class="mt-3">
                <input type="hidden" name="add_shipping" value="1">
                <div class="mb-3">
                    <label for="shipment_number" class="form-label">Номер отгрузки</label>
                    <input type="text" class="form-control" id="shipment_number" name="shipment_number" required>
                </div>
                <div class="mb-3">
                    <label for="shipping_date" class="form-label">Дата отгрузки</label>
                    <input type="date" class="form-control" id="shipping_date" name="shipping_date" required>
                </div>
                <div class="mb-3">
                    <label for="shipment_status" class="form-label">Статус</label>
                    <select class="form-select" id="shipment_status" name="shipment_status" required>
                        <option value="">Выберите статус</option>
                        <option value="В обработке">В обработке</option>
                        <option value="Отправлено">Отправлено</option>
                        <option value="Доставлено">Доставлено</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="employee_id" class="form-label">Сотрудник</label>
                    <select class="form-select" id="employee_id" name="employee_id" required>
                        <option value="">Выберите сотрудника</option>
                        <?php foreach ($employees as $employee): ?>
                            <option value="<?php echo htmlspecialchars($employee['employee_id']); ?>">
                                <?php echo htmlspecialchars($employee['employee_full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="client_id_form" class="form-label">Клиент</label>
                    <select class="form-select" id="client_id_form" name="client_id_form" required>
                        <option value="">Выберите клиента</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?php echo htmlspecialchars($client['client_id']); ?>">
                                <?php echo htmlspecialchars($client['client_company_or_full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Добавить отгрузку</button>
            </form>
        <?php endif; ?>

        <?php if ($role === 'employee'): ?>
            <h3 class="mt-3">Добавить новое техобслуживание</h3>
            <form method="POST" class="mt-3">
                <input type="hidden" name="add_maintenance" value="1">
                <div class="mb-3">
                    <label for="equipment_instance_code" class="form-label">Код оборудования</label>
                    <select class="form-select" id="equipment_instance_code" name="equipment_instance_code" required>
                        <option value="">Выберите оборудование</option>
                        <?php foreach ($equipment_instances as $equipment): ?>
                            <option value="<?php echo htmlspecialchars($equipment['equipment_instance_code']); ?>">
                                <?php echo htmlspecialchars($equipment['equipment_instance_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="maintenance_number" class="form-label">Номер обслуживания</label>
                    <input type="text" class="form-control" id="maintenance_number" name="maintenance_number" required>
                </div>
                <div class="mb-3">
                    <label for="master_id" class="form-label">Мастер</label>
                    <select class="form-select" id="master_id" name="master_id" required>
                        <option value="">Выберите мастера</option>
                        <?php foreach ($masters as $master): ?>
                            <option value="<?php echo htmlspecialchars($master['master_id']); ?>">
                                <?php echo htmlspecialchars($master['master_full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="maintenance_price" class="form-label">Цена</label>
                    <input type="number" step="0.01" min="0" class="form-control" id="maintenance_price" name="maintenance_price" required>
                </div>
                <div class="mb-3">
                    <label for="maintenance_status" class="form-label">Статус</label>
                    <select class="form-select" id="maintenance_status" name="maintenance_status" required>
                        <option value="">Выберите статус</option>
                        <option value="Запланировано">Запланировано</option>
                        <option value="В процессе">В процессе</option>
                        <option value="Завершено">Завершено</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="maintenance_date" class="form-label">Дата обслуживания</label>
                    <input type="date" class="form-control" id="maintenance_date" name="maintenance_date" required>
                </div>
                <button type="submit" class="btn btn-primary">Добавить техобслуживание</button>
            </form>
        <?php endif; ?>

        <?php foreach ($data as $table_name => $table_data): ?>
            <section class="mt-4">
                <h2><?php echo htmlspecialchars(ucfirst($table_name)); ?></h2>
                <?php if ($role === 'admin' || ($role === 'accountant' && in_array($table_name, ['client', 'maintenance', 'product', 'shipping', 'supply', 'supplier']))): ?>
                    <button type="button" class="btn btn-primary mb-2" onclick="showAddForm('<?php echo htmlspecialchars($table_name); ?>')">Добавить запись</button>
                <?php endif; ?>
                <div id="add-form-<?php echo htmlspecialchars($table_name); ?>" class="add-form" style="display: none;">
                    <h3>Добавить в <?php echo htmlspecialchars(ucfirst($table_name)); ?></h3>
                    <form id="add-form-<?php echo htmlspecialchars($table_name); ?>" onsubmit="addRecord('<?php echo htmlspecialchars($table_name); ?>', event)">
                        <?php foreach ($table_data['headers'] as $index => $header): ?>
                            <?php $field = array_keys($table_data['rows'][0] ?? [])[$index] ?? strtolower(str_replace(' ', '_', $header)); ?>
                            <div class="mb-3">
                                <label for="<?php echo htmlspecialchars($field); ?>-add-<?php echo htmlspecialchars($table_name); ?>" class="form-label"><?php echo htmlspecialchars($header); ?></label>
                                <input type="text" class="form-control" id="<?php echo htmlspecialchars($field); ?>-add-<?php echo htmlspecialchars($table_name); ?>" name="<?php echo htmlspecialchars($field); ?>" required>
                            </div>
                        <?php endforeach; ?>
                        <button type="submit" class="btn btn-success">Сохранить</button>
                        <button type="button" class="btn btn-secondary" onclick="hideAddForm('<?php echo htmlspecialchars($table_name); ?>')">Отмена</button>
                    </form>
                </div>
                <?php if ($role === 'admin' || ($role === 'accountant' && in_array($table_name, ['client', 'maintenance', 'product', 'shipping', 'supply', 'supplier']))): ?>
                    <div id="edit-form-<?php echo htmlspecialchars($table_name); ?>" class="edit-form" style="display: none;">
                        <h3>Редактировать запись в <?php echo htmlspecialchars(ucfirst($table_name)); ?></h3>
                        <form id="edit-form-<?php echo htmlspecialchars($table_name); ?>" onsubmit="submitEditForm('<?php echo htmlspecialchars($table_name); ?>', '<?php echo htmlspecialchars($table_data['rows'][0][array_keys($table_data['rows'][0])[0]] ?? ''); ?>')">
                            <input type="hidden" name="id">
                            <?php foreach ($table_data['headers'] as $index => $header): ?>
                                <?php $field = array_keys($table_data['rows'][0] ?? [])[$index] ?? strtolower(str_replace(' ', '_', $header)); ?>
                                <div class="mb-3">
                                    <label for="<?php echo htmlspecialchars($field); ?>-edit-<?php echo htmlspecialchars($table_name); ?>" class="form-label"><?php echo htmlspecialchars($header); ?></label>
                                    <input type="text" class="form-control" id="<?php echo htmlspecialchars($field); ?>-edit-<?php echo htmlspecialchars($table_name); ?>" name="<?php echo htmlspecialchars($field); ?>" required>
                                </div>
                            <?php endforeach; ?>
                            <button type="submit" class="btn btn-success">Сохранить</button>
                            <button type="button" class="btn btn-secondary" onclick="hideEditForm('<?php echo htmlspecialchars($table_name); ?>')">Отмена</button>
                        </form>
                    </div>
                <?php endif; ?>
                <table class="table table-bordered sortable" data-table-name="<?php echo htmlspecialchars($table_name); ?>">
                    <thead>
                        <tr>
                            <?php foreach ($table_data['headers'] as $index => $header): ?>
                                <?php 
                                $field = array_keys($table_data['rows'][0] ?? [])[$index] ?? strtolower(str_replace(' ', '_', $header));
                                $new_direction = ($sort_table === $table_name && $sort_field === $field && $sort_direction === 'ASC') ? 'DESC' : 'ASC';
                                $icon = ($sort_table === $table_name && $sort_field === $field) ? ($sort_direction === 'ASC' ? '↑' : '↓') : '↕';
                                ?>
                                <th class="sort-header" data-sort="<?php echo htmlspecialchars($field); ?>" data-sort-type="<?php echo htmlspecialchars(in_array($field, ['client_id', 'employee_id', 'master_id', 'equipment_instance_price', 'maintenance_price', 'price', 'stock_quantity', 'supplier_id']) ? 'number' : (in_array($field, ['shipping_date', 'maintenance_date']) ? 'date' : 'string')); ?>">
                                    <a href="?table=<?php echo htmlspecialchars($table_name); ?>&sort=<?php echo htmlspecialchars($field); ?>&direction=<?php echo htmlspecialchars($new_direction); ?>" class="sort-link">
                                        <?php echo htmlspecialchars($header); ?>
                                        <span class="sort-icon"><?php echo $icon; ?></span>
                                    </a>
                                </th>
                            <?php endforeach; ?>
                            <?php if ($role === 'admin' || ($role === 'accountant' && in_array($table_name, ['client', 'maintenance', 'product', 'shipping', 'supply', 'supplier']))): ?>
                                <th>Действия</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($table_data['rows'] as $row): ?>
                            <tr>
                                <?php foreach ($row as $col): ?>
                                    <td><?php echo htmlspecialchars($col ?? ''); ?></td>
                                <?php endforeach; ?>
                                <?php if ($role === 'admin' || ($role === 'accountant' && in_array($table_name, ['client', 'maintenance', 'product', 'shipping', 'supply', 'supplier']))): ?>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" onclick="showEditForm('<?php echo htmlspecialchars($table_name); ?>', '<?php echo htmlspecialchars(json_encode($row, JSON_UNESCAPED_UNICODE)); ?>')">Редактировать</button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteRecord('<?php echo htmlspecialchars($table_name); ?>', '<?php echo htmlspecialchars($row[array_keys($row)[0]]); ?>')">Удалить</button>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        <?php endforeach; ?>
    </main>

    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>