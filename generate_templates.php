<?php
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!is_dir(__DIR__ . '/public/templates')) {
    mkdir(__DIR__ . '/public/templates');
}

// Template Target
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'tahun');
$sheet->setCellValue('B1', 'bulan');
$sheet->setCellValue('C1', 'am');
$sheet->setCellValue('D1', 'entity');
$sheet->setCellValue('E1', 'target');

// Dummy data row
$sheet->setCellValue('A2', '2026');
$sheet->setCellValue('B2', '1');
$sheet->setCellValue('C2', 'Nama Sales Member (Contoh: BREE COFFEE & KITCHEN)');
$sheet->setCellValue('D2', 'Nama / Kode Entity (Contoh: BAHANA)');
$sheet->setCellValue('E2', '10000000');

foreach (range('A','E') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$writer = new Xlsx($spreadsheet);
$writer->save(__DIR__ . '/public/templates/template_target.xlsx');

// Template Realisasi
$spreadsheet2 = new Spreadsheet();
$sheet2 = $spreadsheet2->getActiveSheet();
$sheet2->setCellValue('A1', 'tahun');
$sheet2->setCellValue('B1', 'bulan');
$sheet2->setCellValue('C1', 'am');
$sheet2->setCellValue('D1', 'entity');
$sheet2->setCellValue('E1', 'realisasi');

$sheet2->setCellValue('A2', '2026');
$sheet2->setCellValue('B2', '1');
$sheet2->setCellValue('C2', 'Nama Sales Member (Contoh: BREE COFFEE & KITCHEN)');
$sheet2->setCellValue('D2', 'Nama / Kode Entity (Contoh: BAHANA)');
$sheet2->setCellValue('E2', '8000000');

foreach (range('A','E') as $col) {
    $sheet2->getColumnDimension($col)->setAutoSize(true);
}

$writer2 = new Xlsx($spreadsheet2);
$writer2->save(__DIR__ . '/public/templates/template_realisasi.xlsx');

echo "Templates generated successfully.\n";
