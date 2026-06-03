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
        SalesRealization::updateOrCreate(
            $request->only(['year', 'month', 'sales_member_id', 'entity_id']),
            ['realization_amount' => $request->realization_amount]
        );
        return redirect()->route('sales-realizations.index')->with('success', 'Realisasi berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(SalesRealization $sales_realization)
    {
        $sales_members = SalesMember::all();
        $entities = Entity::all();
        return view('sales-realizations.edit', compact('sales_realization', 'sales_members', 'entities'));
    }

    public function update(UpdateSalesRealizationRequest $request, SalesRealization $sales_realization)
    {
        $sales_realization->update($request->validated());
        return redirect()->route('sales-realizations.index')->with('success', 'Realisasi berhasil diupdate.');
    }

    public function destroy(SalesRealization $sales_realization)
    {
        $sales_realization->delete();
        return redirect()->route('sales-realizations.index')->with('success', 'Realisasi berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        try {
            Excel::import(new SalesRealizationImport, $request->file('file'));
            return redirect()->route('sales-realizations.index')->with('success', 'Realisasi berhasil diimport.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            return redirect()->route('sales-realizations.index')->with('error', 'Ada kesalahan pada baris excel. Pastikan data valid.');
        } catch (\Exception $e) {
            return redirect()->route('sales-realizations.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
