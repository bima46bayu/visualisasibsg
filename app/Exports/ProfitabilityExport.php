<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\Entity;
use App\Models\ProfitabilityItem;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Protection;

class ProfitabilityExport implements WithHeadings, WithEvents, FromCollection, WithMapping
{
    protected $isTemplate;

    public function __construct($isTemplate = false)
    {
        $this->isTemplate = $isTemplate;
    }

    public function collection()
    {
        if ($this->isTemplate) {
            return collect([]);
        }

        return ProfitabilityItem::with(['profitability.entity'])->get();
    }

    public function map($item): array
    {
        if ($this->isTemplate) {
            return [];
        }

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return [
            $item->profitability->year ?? '',
            $months[$item->profitability->month ?? 1] ?? '',
            isset($item->profitability->subEntity) 
                ? ($item->profitability->entity->name . ' - ' . $item->profitability->subEntity->name) 
                : ($item->profitability->entity->name ?? ''),
            $item->category,
            $item->description,
            $item->amount,
        ];
    }

    public function headings(): array
    {
        return [
            'Tahun',
            'Bulan',
            'Entity',
            'Kategori',
            'Deskripsi',
            'Jumlah'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Validation for Kategori
                $validationCat = $sheet->getDataValidation('D2:D1000');
                $validationCat->setType(DataValidation::TYPE_LIST);
                $validationCat->setErrorStyle(DataValidation::STYLE_STOP);
                $validationCat->setAllowBlank(false);
                $validationCat->setShowDropDown(true);
                $validationCat->setFormula1('"pendapatan,hpp,biaya_marketing,biaya_admin,biaya_non_ops,pendapatan_lain,biaya_lain,pajak"');

                // Validation for Bulan
                $validationMonth = $sheet->getDataValidation('B2:B1000');
                $validationMonth->setType(DataValidation::TYPE_LIST);
                $validationMonth->setErrorStyle(DataValidation::STYLE_STOP);
                $validationMonth->setAllowBlank(false);
                $validationMonth->setShowDropDown(true);
                $validationMonth->setFormula1('"Januari,Februari,Maret,April,Mei,Juni,Juli,Agustus,September,Oktober,November,Desember"');

                // Load entities and sub entities
                $entitiesData = [];
                $entities = Entity::with('subEntities')->get();
                foreach ($entities as $entity) {
                    if ($entity->subEntities->count() > 0) {
                        foreach ($entity->subEntities as $sub) {
                            $entitiesData[] = $entity->name . ' - ' . $sub->name;
                        }
                        $entitiesData[] = $entity->name; // In case they just want the main entity
                    } else {
                        $entitiesData[] = $entity->name;
                    }
                }

                // Create hidden list sheet to bypass 255 chars limit
                $spreadsheet = $event->sheet->getDelegate()->getParent();
                $listSheet = $spreadsheet->createSheet();
                $listSheet->setTitle('Lists');
                $listSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
                
                foreach ($entitiesData as $index => $ent) { 
                    $listSheet->setCellValue('A' . ($index + 1), $ent); 
                }
                
                $spreadsheet->addNamedRange(new \PhpOffice\PhpSpreadsheet\NamedRange('EntityList', $listSheet, 'Lists!$A$1:$A$' . count($entitiesData)));
                
                // Back to main sheet
                $spreadsheet->setActiveSheetIndex(0);

                // Validation for Entity using NamedRange
                $validationEnt = $sheet->getDataValidation('C2:C1000');
                $validationEnt->setType(DataValidation::TYPE_LIST);
                $validationEnt->setErrorStyle(DataValidation::STYLE_STOP);
                $validationEnt->setAllowBlank(false);
                $validationEnt->setShowDropDown(true);
                $validationEnt->setFormula1('=EntityList');

                // Protect Header Row
                $sheet->getProtection()->setSheet(true);
                $sheet->getProtection()->setPassword('bsg2026'); // Secret password
                
                // Unlock A2:F1000 so user can edit them
                $sheet->getStyle('A2:F1000')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                
                // Auto-size columns
                foreach (range('A', 'F') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}
