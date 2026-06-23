<?php

namespace App\Http\Controllers;

use App\Models\SalesTarget;
use App\Models\SalesMember;
use App\Models\Entity;
use App\Models\EndUser;
use App\Http\Requests\StoreSalesTargetRequest;
use App\Http\Requests\UpdateSalesTargetRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SalesTargetImport;

class SalesTargetController extends Controller
{
    public function export(Request $request)
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

    public function index(Request $request)
    {
        $query = SalesTarget::with(['salesMember', 'entity']);
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }
        if ($request->has('month') && $request->month) {
            $query->where('month', $request->month);
        }
        $sales_targets = $query->orderBy('year', 'desc')->orderBy('month', 'desc')->paginate(10);
        return view('sales-targets.index', compact('sales_targets'));
    }

    public function create()
    {
        $sales_members = SalesMember::all();
        $entities = Entity::all();
        return view('sales-targets.create', compact('sales_members', 'entities'));
    }

    public function store(StoreSalesTargetRequest $request)
    {
        foreach ($request->targets as $targetData) {
            $amName = trim($targetData['sales_member_name']);
            $entityName = trim($targetData['entity_name']);
            
            $salesMember = SalesMember::firstOrCreate(
                ['name' => $amName],
                ['code' => 'SM-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $amName), 0, 3)) . '-' . rand(1000, 9999)]
            );
            $entity = Entity::firstOrCreate(
                ['name' => $entityName],
                ['code' => 'ENT-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $entityName), 0, 3)) . '-' . rand(1000, 9999)]
            );
            $endUser = EndUser::firstOrCreate(['name' => trim($targetData['end_user_name'])]);

            SalesTarget::updateOrCreate(
                [
                    'year' => $targetData['year'],
                    'month' => $targetData['month'],
                    'sales_member_id' => $salesMember->id,
                    'entity_id' => $entity->id,
                    'end_user_id' => $endUser->id
                ],
                ['target_amount' => $targetData['target_amount']]
            );
        }
        return redirect()->route('sales-management.index', ['tab' => 'target'])->with('success', count($request->targets) . ' Target berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(SalesTarget $target)
    {
        $sales_members = SalesMember::all();
        $entities = Entity::all();
        return view('sales-targets.edit', compact('target', 'sales_members', 'entities'));
    }

    public function update(UpdateSalesTargetRequest $request, SalesTarget $target)
    {
        $amName = trim($request->sales_member_name);
        $entityName = trim($request->entity_name);
        
        $salesMember = SalesMember::firstOrCreate(
            ['name' => $amName],
            ['code' => 'SM-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $amName), 0, 3)) . '-' . rand(1000, 9999)]
        );
        $entity = Entity::firstOrCreate(
            ['name' => $entityName],
            ['code' => 'ENT-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $entityName), 0, 3)) . '-' . rand(1000, 9999)]
        );
        $endUser = EndUser::firstOrCreate(['name' => trim($request->end_user_name)]);

        $existing = SalesTarget::where('year', $request->year)
            ->where('month', $request->month)
            ->where('sales_member_id', $salesMember->id)
            ->where('entity_id', $entity->id)
            ->where('end_user_id', $endUser->id)
            ->where('id', '!=', $target->id)
            ->first();

        if ($existing) {
            $existing->delete(); // Timpa data lama (hapus yang lama, gunakan yang sedang diedit)
        }

        $target->update([
            'year' => $request->year,
            'month' => $request->month,
            'sales_member_id' => $salesMember->id,
            'entity_id' => $entity->id,
            'end_user_id' => $endUser->id,
            'target_amount' => $request->target_amount
        ]);
        return redirect()->route('sales-management.index', ['tab' => 'target'])->with('success', 'Target berhasil diupdate.');
    }

    public function destroy(SalesTarget $target)
    {
        $target->delete();
        return redirect()->route('sales-management.index', ['tab' => 'target'])->with('success', 'Target berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        try {
            Excel::import(new SalesTargetImport, $request->file('file'));
            return redirect()->route('sales-management.index', ['tab' => 'target'])->with('success', 'Target berhasil diimport.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMsg = 'Kesalahan pada excel: ';
            foreach ($failures as $failure) {
                $errorMsg .= 'Baris ' . $failure->row() . ' (' . implode(', ', $failure->errors()) . ') | Data terbaca: ' . json_encode($failure->values());
                break; // Ambil error pertama saja
            }
            return redirect()->route('sales-management.index', ['tab' => 'target'])->with('error', trim($errorMsg));
        } catch (\Exception $e) {
            return redirect()->route('sales-management.index', ['tab' => 'target'])->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
