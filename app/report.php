<?php
session_start();
require __DIR__ . '/db.php'; // Подключение к базе данных
require __DIR__ . '/fpdf/fpdf.php'; // Подключение FPDF
require __DIR__ . '/SimpleXLSXGen.php'; // Подключение SimpleXLSXGen

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];

// Определение доступных таблиц для каждой роли
$allowed_tables = [
    'admin' => ['client', 'employee', 'master', 'equipment_instance', 'maintenance', 'product', 'shipping', 'supply', 'supplier'],
    'accountant' => ['client', 'employee', 'maintenance', 'product', 'shipping', 'supply', 'supplier'],
    'employee' => ['product', 'equipment_instance', 'shipping'],
    'master' => ['equipment_instance']
];

// Загрузка данных для разрешенных таблиц
$data = [];
foreach ($allowed_tables[$role] as $table) {
    try {
        switch ($table) {
            case 'client':
                $stmt = $pdo->query('SELECT client_id, client_email, client_phone, client_address, client_company_or_full_name FROM client ORDER BY client_id');
                $data['client'] = ['headers' => ['ID', 'Email', 'Телефон', 'Адрес', 'Компания'], 'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
                break;
            case 'employee':
                $stmt = $pdo->query('SELECT e.employee_id, e.employee_full_name, e.employee_phone, s.shipment_number 
                                     FROM employee e 
                                     LEFT JOIN shipping s ON e.employee_id = s.employee_id 
                                     ORDER BY e.employee_id');
                $data['employee'] = ['headers' => ['ID', 'ФИО', 'Телефон', 'Номер отгрузки'], 'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
                break;
            case 'master':
                $stmt = $pdo->query('SELECT master_id, master_full_name, master_phone FROM master ORDER BY master_full_name');
                $data['master'] = ['headers' => ['ID', 'ФИО', 'Телефон'], 'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
                break;
            case 'equipment_instance':
                $stmt = $pdo->query('SELECT equipment_instance_code, equipment_instance_name, employee_id, equipment_instance_status, equipment_instance_price 
                                     FROM equipment_instance ORDER BY equipment_instance_code');
                $data['equipment_instance'] = ['headers' => ['Код', 'Название', 'Сотрудник', 'Статус', 'Цена'], 'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
                break;
            case 'maintenance':
                $stmt = $pdo->query('SELECT equipment_instance_code, maintenance_number, master_id, maintenance_price, maintenance_status, maintenance_date 
                                     FROM maintenance ORDER BY maintenance_date');
                $data['maintenance'] = ['headers' => ['Код оборудования', 'Номер обслуживания', 'Мастер', 'Цена', 'Статус', 'Дата'], 'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
                break;
            case 'product':
                $stmt = $pdo->query('SELECT product_code, product_name, price, stock_quantity, product_unit_of_measurement 
                                     FROM product ORDER BY product_name');
                $data['product'] = ['headers' => ['Код', 'Название', 'Цена', 'Остаток', 'Единица измерения'], 'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
                break;
            case 'shipping':
                $stmt = $pdo->query('SELECT s.shipment_number, s.shipping_date, s.shipment_status, e.employee_full_name, s.client_id 
                                     FROM shipping s 
                                     JOIN employee e ON s.employee_id = e.employee_id 
                                     ORDER BY s.shipment_number');
                $data['shipping'] = ['headers' => ['Номер отгрузки', 'Дата', 'Статус', 'Сотрудник', 'Клиент'], 'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
                break;
            case 'supply':
                $stmt = $pdo->query('SELECT s.supply_number, s.supply_status, s.supplier_id, sp.supplier_email 
                                     FROM supply s 
                                     JOIN supplier sp ON s.supplier_id = sp.supplier_id 
                                     ORDER BY s.supply_number');
                $data['supply'] = ['headers' => ['Номер поставки', 'Статус', 'ID поставщика', 'Email поставщика'], 'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
                break;
            case 'supplier':
                $stmt = $pdo->query('SELECT supplier_id, supplier_company_or_full_name, supplier_email, supplier_phone, supplier_address 
                                     FROM supplier ORDER BY supplier_company_or_full_name');
                $data['supplier'] = ['headers' => ['ID', 'Компания', 'Email', 'Телефон', 'Адрес'], 'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
                break;
        }
    } catch (PDOException $e) {
        die("Ошибка при запросе к таблице $table: " . $e->getMessage());
    }
}

$format = $_GET['format'] ?? 'pdf';

if ($format === 'pdf') {
    // Создание PDF
    class PDF extends FPDF {
        function Header() {
            $this->AddFont('courier', '', 'courier.php'); // Добавление шрифта внутри класса
            $this->SetFont('courier', '', 16); // Используем 'DejaVu' вместо 'DejaVuSans'
            $this->Cell(0, 10, 'Отчет для ' . htmlspecialchars($GLOBALS['username']), 0, 1, 'C');
            $this->Ln(10);
        }

        function Footer() {
            $this->SetFont('courier', '', 8);
            $this->Cell(0, 10, 'Страница ' . $this->PageNo(), 0, 0, 'C');
        }
    }

    // Создание экземпляра PDF
    $pdf = new PDF();
    $pdf->AddFont('courier', '', 'courier.php'); // Убедитесь, что файл шрифта существует
    $pdf->AddPage();
    $pdf->SetFont('courier', '', 12);

    foreach ($data as $table_name => $table_data) {
        // Название таблицы
        $pdf->SetFont('courier', '', 12);
        $pdf->Cell(0, 10, ucfirst($table_name), 0, 1);
        $pdf->Ln(5);

        // Заголовки
        $pdf->SetFont('courier', '', 10);
        foreach ($table_data['headers'] as $header) {
            $pdf->Cell(38, 7, htmlspecialchars($header), 1);
        }
        $pdf->Ln();

        // Данные
        foreach ($table_data['rows'] as $row) {
            foreach ($row as $col) {
                $pdf->Cell(38, 6, htmlspecialchars($col ?? '—'), 1);
            }
            $pdf->Ln();
        }

        $pdf->Ln(10);
    }

    $pdf->Output('D', 'report_' . $username . '.pdf');
    exit;
} elseif ($format === 'excel') {
    // Создание временного CSV-файла
    $csv_file = __DIR__ . '/tmp/report_' . $username . '.csv';
    $output = fopen($csv_file, 'w');
    if (!$output) {
        die("Не удалось создать CSV-файл.");
    }

    // Добавление BOM для кириллицы
    fwrite($output, "\xEF\xBB\xBF");

    foreach ($data as $table_name => $table_data) {
        // Название таблицы
        fputcsv($output, [ucfirst($table_name)], ';', '"', '"');

        // Заголовки
        fputcsv($output, $table_data['headers'], ';', '"', '"');

        // Данные
        foreach ($table_data['rows'] as $row) {
            $row_data = array_map(function($col) {
                return $col ?? '—';
            }, $row);
            fputcsv($output, $row_data, ';', '"', '"');
        }

        // Пустая строка
        fputcsv($output, [], ';', '"', '"');
    }
    fclose($output);

    // Чтение CSV для XLSX
    $xlsx_data = [];
    $current_table = '';
    $table_data = [];
    $csv = file($csv_file);

    foreach ($csv as $line) {
        $row = str_getcsv($line, ';', '"', '"');
        if (isset($row[0]) && count($row) === 1 && trim($row[0]) !== '') {
            if ($current_table && $table_data) {
                $xlsx_data[$current_table] = $table_data;
                $table_data = [];
            }
            $current_table = trim($row[0]) ?: 'Без названия';
        } elseif (isset($row[0]) && count($row) >= 1 && trim(implode('', $row)) !== '') {
            $table_data[] = array_map(function($col) {
                return trim($col) ?: '—';
            }, $row);
        }
    }
    if ($current_table && $table_data) {
        $xlsx_data[$current_table] = $table_data;
    }

    // Создание XLSX
    $xlsx = new SimpleXLSXGen();
$sheet_index = 0;

foreach ($xlsx_data as $table_name => $rows) {
    // Добавляем лист с данными и именем
    $xlsx->addSheet($rows, $table_name);
    $sheet_index++;
}

unlink($csv_file); // Удаление временного файла
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="report_' . $username . '.xlsx"');
header('Cache-Control: max-age=0');

// Генерация и отправка файла
$xlsx->downloadAs('report_' . $username . '.xlsx');
    exit;
} else {
    echo "Неверный формат.";
}
?>