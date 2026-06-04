<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\NamedRange;

if (!is_dir(__DIR__ . '/public/templates')) {
    mkdir(__DIR__ . '/public/templates');
}

$salesMembers = \App\Models\SalesMember::pluck('name')->toArray();
$entities = \App\Models\Entity::pluck('name')->toArray();

// Pastikan ada setidaknya satu data agar template tidak kosong
if (empty($salesMembers)) $salesMembers = ['BREE COFFEE & KITCHEN', 'Contoh Sales 2'];
if (empty($entities)) $entities = ['BAHANA', 'Contoh Entity 2'];

function addValidation($sheet, $col, $dataRange) {
    // Terapkan ke baris 2 sampai 1000
    for ($i = 2; $i <= 1000; $i++) {
        $validation = $sheet->getCell($col . $i)->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(true);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Pilihan tidak valid');
        $validation->setError('Nilai yang dimasukkan tidak ada dalam daftar.');
        $validation->setPromptTitle('Pilih dari daftar');
        $validation->setPrompt('Silakan pilih salah satu nilai dari dropdown.');
        $validation->setFormula1($dataRange);
    }
}

function createTemplate($filename, $valCol, $salesMembers, $entities) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Data');
    
    // Header
    $sheet->setCellValue('A1', 'tahun');
    $sheet->setCellValue('B1', 'bulan');
    $sheet->setCellValue('C1', 'am');
    $sheet->setCellValue('D1', 'entity');
    $sheet->setCellValue('E1', $valCol);

    $months_names = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    // Dummy data row
    $sheet->setCellValue('A2', date('Y'));
    $sheet->setCellValue('B2', $months_names[date('n') - 1]);
    $sheet->setCellValue('C2', $salesMembers[0]);
    $sheet->setCellValue('D2', $entities[0]);
    $sheet->setCellValue('E2', $valCol == 'target' ? '10000000' : '8000000');

    // Create a hidden sheet for lists
    $listSheet = $spreadsheet->createSheet();
    $listSheet->setTitle('Lists');
    $listSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
    
    // Years 2024 to 2028 (2 years before and after current)
    $years = range(date('Y') - 2, date('Y') + 2);
    // Months 1 to 12
    $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    // Populate lists
    foreach ($years as $index => $year) { $listSheet->setCellValue('A' . ($index + 1), $year); }
    foreach ($months as $index => $month) { $listSheet->setCellValue('B' . ($index + 1), $month); }
    foreach ($salesMembers as $index => $sm) { $listSheet->setCellValue('C' . ($index + 1), $sm); }
    foreach ($entities as $index => $ent) { $listSheet->setCellValue('D' . ($index + 1), $ent); }
    
    // Create Named Ranges for dropdowns (using absolute references for the ranges)
    $spreadsheet->addNamedRange(new NamedRange('YearList', $listSheet, 'Lists!$A$1:$A$' . count($years)));
    $spreadsheet->addNamedRange(new NamedRange('MonthList', $listSheet, 'Lists!$B$1:$B$' . count($months)));
    $spreadsheet->addNamedRange(new NamedRange('AmList', $listSheet, 'Lists!$C$1:$C$' . count($salesMembers)));
    $spreadsheet->addNamedRange(new NamedRange('EntityList', $listSheet, 'Lists!$D$1:$D$' . count($entities)));

    // Apply data validation to columns A, B, C, D
    addValidation($sheet, 'A', '=YearList');
    addValidation($sheet, 'B', '=MonthList');
    addValidation($sheet, 'C', '=AmList');
    addValidation($sheet, 'D', '=EntityList');

    // Auto-size columns
    foreach (range('A','E') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    // Set active sheet back to Data
    $spreadsheet->setActiveSheetIndex(0);

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/public/templates/' . $filename);
}

createTemplate('template_target.xlsx', 'target', $salesMembers, $entities);
createTemplate('template_realisasi.xlsx', 'realisasi', $salesMembers, $entities);

echo "Templates generated successfully.\n";
