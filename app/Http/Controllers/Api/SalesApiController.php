<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DashboardService;
use App\Models\SalesTarget;
use App\Models\SalesRealization;
use App\Models\Team;
use App\Models\Entity;
use App\Models\SalesMember;
use App\Models\EndUser;

class SalesApiController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function dashboard(Request $request)
    {
        $data = $this->dashboardService->getDashboardData($request->all());
        return response()->json($data);
    }

    public function masterData()
    {
        return response()->json([
            'teams' => Team::all(),
            'entities' => Entity::all(),
            'sales_members' => SalesMember::all(),
            'end_users' => EndUser::all(),
        ]);
    }

    public function getTargets(Request $request)
    {
        $query = SalesTarget::with(['salesMember', 'entity', 'endUser']);
        if ($request->year) $query->where('year', $request->year);
        if ($request->month) $query->where('month', $request->month);
        
        return response()->json($query->orderBy('year', 'desc')->orderBy('month', 'desc')->paginate(10));
    }

    public function storeTarget(Request $request)
    {
        $data = $request->validate([
            'targets' => 'required|array',
            'targets.*.year' => 'required|integer',
            'targets.*.month' => 'required|integer',
            'targets.*.sales_member_name' => 'required|string',
            'targets.*.entity_name' => 'required|string',
            'targets.*.end_user_name' => 'required|string',
            'targets.*.target_amount' => 'required|numeric',
        ]);

        foreach ($data['targets'] as $t) {
            $salesMember = SalesMember::firstOrCreate(['name' => $t['sales_member_name']]);
            $entity = Entity::firstOrCreate(['name' => $t['entity_name']]);
            $endUser = EndUser::firstOrCreate(['name' => $t['end_user_name']]);

            SalesTarget::create([
                'year' => $t['year'],
                'month' => $t['month'],
                'sales_member_id' => $salesMember->id,
                'entity_id' => $entity->id,
                'end_user_id' => $endUser->id,
                'target_amount' => $t['target_amount'],
            ]);
        }

        return response()->json(['message' => 'Targets saved successfully']);
    }

    public function destroyTarget($id)
    {
        SalesTarget::findOrFail($id)->delete();
        return response()->json(['message' => 'Target deleted successfully']);
    }

    public function getRealizations(Request $request)
    {
        $query = SalesRealization::with(['salesMember', 'entity', 'endUser']);
        if ($request->year) $query->where('year', $request->year);
        if ($request->month) $query->where('month', $request->month);
        
        return response()->json($query->orderBy('year', 'desc')->orderBy('month', 'desc')->paginate(10));
    }

    public function storeRealization(Request $request)
    {
        $data = $request->validate([
            'realizations' => 'required|array',
            'realizations.*.year' => 'required|integer',
            'realizations.*.month' => 'required|integer',
            'realizations.*.sales_member_name' => 'required|string',
            'realizations.*.entity_name' => 'required|string',
            'realizations.*.end_user_name' => 'required|string',
            'realizations.*.realization_amount' => 'required|numeric',
        ]);

        foreach ($data['realizations'] as $r) {
            $salesMember = SalesMember::firstOrCreate(['name' => $r['sales_member_name']]);
            $entity = Entity::firstOrCreate(['name' => $r['entity_name']]);
            $endUser = EndUser::firstOrCreate(['name' => $r['end_user_name']]);

            SalesRealization::create([
                'year' => $r['year'],
                'month' => $r['month'],
                'sales_member_id' => $salesMember->id,
                'entity_id' => $entity->id,
                'end_user_id' => $endUser->id,
                'realization_amount' => $r['realization_amount'],
            ]);
        }

        return response()->json(['message' => 'Realizations saved successfully']);
    }

    public function destroyRealization($id)
    {
        SalesRealization::findOrFail($id)->delete();
        return response()->json(['message' => 'Realization deleted successfully']);
    }
    public function importTargets(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\SalesTargetImport, $request->file('file'));
            return response()->json(['message' => 'Target berhasil diimport.']);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMsg = 'Kesalahan pada excel: ';
            foreach ($failures as $failure) {
                $errorMsg .= 'Baris ' . $failure->row() . ' (' . implode(', ', $failure->errors()) . ') ';
                break;
            }
            return response()->json(['error' => trim($errorMsg)], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal import: ' . $e->getMessage()], 400);
        }
    }

    public function importRealizations(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\SalesRealizationImport, $request->file('file'));
            return response()->json(['message' => 'Realisasi berhasil diimport.']);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMsg = 'Kesalahan pada excel: ';
            foreach ($failures as $failure) {
                $errorMsg .= 'Baris ' . $failure->row() . ' (' . implode(', ', $failure->errors()) . ') ';
                break;
            }
            return response()->json(['error' => trim($errorMsg)], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal import: ' . $e->getMessage()], 400);
        }
    }

    public function exportTargets(Request $request)
    {
        $filename = 'export_targets_' . date('Ymd_His') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SalesTargetExport(
                $request->start_year,
                $request->start_month,
                $request->end_year,
                $request->end_month
            ),
            $filename
        );
    }

    public function exportRealizations(Request $request)
    {
        $filename = 'export_realisasi_' . date('Ymd_His') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SalesRealizationExport(
                $request->start_year,
                $request->start_month,
                $request->end_year,
                $request->end_month
            ),
            $filename
        );
    }

    private function getMasterModel($type)
    {
        switch ($type) {
            case 'teams': return new Team();
            case 'entities': return new Entity();
            case 'sales_members': return new SalesMember();
            case 'end_users': return new EndUser();
            default: abort(400, 'Invalid master type');
        }
    }

    public function getMasterList(Request $request, $type)
    {
        $model = $this->getMasterModel($type);
        return response()->json($model->orderBy('id', 'desc')->paginate(10));
    }

    public function storeMaster(Request $request, $type)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $model = $this->getMasterModel($type);
        $data = ['name' => $request->name];
        if ($request->has('code')) $data['code'] = $request->code;
        $created = $model->create($data);
        return response()->json(['message' => 'Berhasil ditambahkan', 'data' => $created]);
    }

    public function updateMaster(Request $request, $type, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $model = $this->getMasterModel($type)->findOrFail($id);
        $data = ['name' => $request->name];
        if ($request->has('code')) $data['code'] = $request->code;
        $model->update($data);
        return response()->json(['message' => 'Berhasil diupdate', 'data' => $model]);
    }

    public function destroyMaster($type, $id)
    {
        $model = $this->getMasterModel($type)->findOrFail($id);
        $model->delete();
        return response()->json(['message' => 'Berhasil dihapus']);
    }
    public function downloadTemplate($type)
    {
        if (!in_array($type, ['target', 'realisasi'])) {
            abort(404, 'Invalid template type');
        }

        $salesMembers = \App\Models\SalesMember::pluck('name')->toArray();
        $entities = \App\Models\Entity::pluck('name')->toArray();
        $endUsers = \App\Models\EndUser::pluck('name')->toArray();

        if (empty($salesMembers)) $salesMembers = ['BREE COFFEE & KITCHEN', 'Contoh Sales 2'];
        if (empty($entities)) $entities = ['BAHANA', 'Contoh Entity 2'];
        if (empty($endUsers)) $endUsers = ['Umum', 'Contoh End User 2'];

        $valCol = $type === 'target' ? 'target' : 'realisasi';
        $filename = "template_{$type}.xlsx";

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data');
        
        // Header
        $sheet->setCellValue('A1', 'tahun');
        $sheet->setCellValue('B1', 'bulan');
        $sheet->setCellValue('C1', 'am');
        $sheet->setCellValue('D1', 'entity');
        $sheet->setCellValue('E1', 'end_user');
        $sheet->setCellValue('F1', $valCol);

        $months_names = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $sheet->setCellValue('A2', date('Y'));
        $sheet->setCellValue('B2', $months_names[date('n') - 1]);
        $sheet->setCellValue('C2', $salesMembers[0]);
        $sheet->setCellValue('D2', $entities[0]);
        $sheet->setCellValue('E2', $endUsers[0]);
        $sheet->setCellValue('F2', $valCol == 'target' ? '10000000' : '8000000');

        $listSheet = $spreadsheet->createSheet();
        $listSheet->setTitle('Lists');
        $listSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
        
        $years = range(date('Y') - 2, date('Y') + 2);
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        foreach ($years as $index => $year) { $listSheet->setCellValue('A' . ($index + 1), $year); }
        foreach ($months as $index => $month) { $listSheet->setCellValue('B' . ($index + 1), $month); }
        foreach ($salesMembers as $index => $sm) { $listSheet->setCellValue('C' . ($index + 1), $sm); }
        foreach ($entities as $index => $ent) { $listSheet->setCellValue('D' . ($index + 1), $ent); }
        foreach ($endUsers as $index => $eu) { $listSheet->setCellValue('E' . ($index + 1), $eu); }
        
        $spreadsheet->addNamedRange(new \PhpOffice\PhpSpreadsheet\NamedRange('YearList', $listSheet, 'Lists!$A$1:$A$' . count($years)));
        $spreadsheet->addNamedRange(new \PhpOffice\PhpSpreadsheet\NamedRange('MonthList', $listSheet, 'Lists!$B$1:$B$' . count($months)));
        $spreadsheet->addNamedRange(new \PhpOffice\PhpSpreadsheet\NamedRange('AmList', $listSheet, 'Lists!$C$1:$C$' . count($salesMembers)));
        $spreadsheet->addNamedRange(new \PhpOffice\PhpSpreadsheet\NamedRange('EntityList', $listSheet, 'Lists!$D$1:$D$' . count($entities)));
        $spreadsheet->addNamedRange(new \PhpOffice\PhpSpreadsheet\NamedRange('EndUserList', $listSheet, 'Lists!$E$1:$E$' . count($endUsers)));

        $addValidation = function($sheet, $col, $dataRange) {
            for ($i = 2; $i <= 1000; $i++) {
                $validation = $sheet->getCell($col . $i)->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(true);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Informasi Pilihan');
                $validation->setError('Anda bisa memilih dari daftar, atau mengetik nama baru. Nama baru akan otomatis ditambahkan ke database.');
                $validation->setPromptTitle('Pilih dari daftar');
                $validation->setPrompt('Silakan pilih atau ketik nama baru.');
                $validation->setFormula1($dataRange);
            }
        };

        $addValidation($sheet, 'A', '=YearList');
        $addValidation($sheet, 'B', '=MonthList');
        $addValidation($sheet, 'C', '=AmList');
        $addValidation($sheet, 'D', '=EntityList');
        $addValidation($sheet, 'E', '=EndUserList');

        foreach (range('A','F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
