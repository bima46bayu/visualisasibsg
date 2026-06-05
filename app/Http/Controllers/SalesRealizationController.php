<?php

namespace App\Http\Controllers;

use App\Models\SalesRealization;
use App\Models\SalesMember;
use App\Models\Entity;
use App\Http\Requests\StoreSalesRealizationRequest;
use App\Http\Requests\UpdateSalesRealizationRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SalesRealizationImport;

class SalesRealizationController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesRealization::with(['salesMember', 'entity']);
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }
        if ($request->has('month') && $request->month) {
            $query->where('month', $request->month);
        }
        $sales_realizations = $query->orderBy('year', 'desc')->orderBy('month', 'desc')->paginate(10);
        return view('sales-realizations.index', compact('sales_realizations'));
    }

    public function create()
    {
        $sales_members = SalesMember::all();
        $entities = Entity::all();
        return view('sales-realizations.create', compact('sales_members', 'entities'));
    }

    public function store(StoreSalesRealizationRequest $request)
    {
        foreach ($request->realizations as $realizationData) {
            $salesMember = SalesMember::firstOrCreate(['name' => $realizationData['sales_member_name']]);
            $entity = Entity::firstOrCreate(['name' => $realizationData['entity_name']]);

            SalesRealization::updateOrCreate(
                [
                    'year' => $realizationData['year'],
                    'month' => $realizationData['month'],
                    'sales_member_id' => $salesMember->id,
                    'entity_id' => $entity->id
                ],
                ['realization_amount' => $realizationData['realization_amount']]
            );
        }
        return redirect()->route('sales-management.index', ['tab' => 'realisasi'])->with('success', count($request->realizations) . ' Realisasi berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(SalesRealization $realization)
    {
        $sales_members = SalesMember::all();
        $entities = Entity::all();
        return view('sales-realizations.edit', compact('realization', 'sales_members', 'entities'));
    }

    public function update(UpdateSalesRealizationRequest $request, SalesRealization $realization)
    {
        $salesMember = SalesMember::firstOrCreate(['name' => $request->sales_member_name]);
        $entity = Entity::firstOrCreate(['name' => $request->entity_name]);

        $existing = SalesRealization::where('year', $request->year)
            ->where('month', $request->month)
            ->where('sales_member_id', $salesMember->id)
            ->where('entity_id', $entity->id)
            ->where('id', '!=', $realization->id)
            ->first();

        if ($existing) {
            $existing->delete(); // Timpa data lama (hapus yang lama, gunakan yang sedang diedit)
        }

        $realization->update([
            'year' => $request->year,
            'month' => $request->month,
            'sales_member_id' => $salesMember->id,
            'entity_id' => $entity->id,
            'realization_amount' => $request->realization_amount
        ]);
        return redirect()->route('sales-management.index', ['tab' => 'realisasi'])->with('success', 'Realisasi berhasil diupdate.');
    }

    public function destroy(SalesRealization $realization)
    {
        $realization->delete();
        return redirect()->route('sales-management.index', ['tab' => 'realisasi'])->with('success', 'Realisasi berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        try {
            Excel::import(new SalesRealizationImport, $request->file('file'));
            return redirect()->route('sales-management.index', ['tab' => 'realisasi'])->with('success', 'Realisasi berhasil diimport.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMsg = 'Kesalahan pada excel: ';
            foreach ($failures as $failure) {
                $errorMsg .= 'Baris ' . $failure->row() . ' (' . implode(', ', $failure->errors()) . ') ';
                break; // Ambil error pertama saja agar pesan tidak terlalu panjang
            }
            return redirect()->route('sales-management.index', ['tab' => 'realisasi'])->with('error', trim($errorMsg));
        } catch (\Exception $e) {
            return redirect()->route('sales-management.index', ['tab' => 'realisasi'])->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
