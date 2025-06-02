<?php
require __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/setasign/fpdf/fpdf.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$format = $_GET['format'] ?? 'pdf';
$data = array_map('str_getcsv', file(__DIR__ . '/data.csv'));
$headers = array_shift($data);

if ($format === 'pdf') {
    class PDF extends \FPDF {
        function Header() {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, 'Отчет по данным', 0, 1, 'C');
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 10);

    foreach ($headers as $col) {
        $pdf->Cell(40, 7, $col, 1);
    }
    $pdf->Ln();

    foreach ($data as $row) {
        foreach ($row as $col) {
            $pdf->Cell(40, 6, iconv('UTF-8', 'windows-1252', $col), 1);
        }
        $pdf->Ln();
    }

    $pdf->Output('D', 'report.pdf');
    exit;

} elseif ($format === 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->fromArray([$headers], NULL, 'A1');
    $sheet->fromArray($data, NULL, 'A2');

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="report.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
} else {
    echo "Неверный формат.";
}
