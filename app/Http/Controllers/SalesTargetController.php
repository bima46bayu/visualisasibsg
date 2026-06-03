<?php

namespace App\Http\Controllers;

use App\Models\SalesTarget;
use App\Models\SalesMember;
use App\Models\Entity;
use App\Http\Requests\StoreSalesTargetRequest;
use App\Http\Requests\UpdateSalesTargetRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SalesTargetImport;

class SalesTargetController extends Controller
{
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
        SalesTarget::updateOrCreate(
            $request->only(['year', 'month', 'sales_member_id', 'entity_id']),
            ['target_amount' => $request->target_amount]
        );
        return redirect()->route('sales-targets.index')->with('success', 'Target berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(SalesTarget $sales_target)
    {
        $sales_members = SalesMember::all();
        $entities = Entity::all();
        return view('sales-targets.edit', compact('sales_target', 'sales_members', 'entities'));
    }

    public function update(UpdateSalesTargetRequest $request, SalesTarget $sales_target)
    {
        $sales_target->update($request->validated());
        return redirect()->route('sales-targets.index')->with('success', 'Target berhasil diupdate.');
    }

    public function destroy(SalesTarget $sales_target)
    {
        $sales_target->delete();
        return redirect()->route('sales-targets.index')->with('success', 'Target berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        try {
            Excel::import(new SalesTargetImport, $request->file('file'));
            return redirect()->route('sales-targets.index')->with('success', 'Target berhasil diimport.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            return redirect()->route('sales-targets.index')->with('error', 'Ada kesalahan pada baris excel. Pastikan data valid.');
        } catch (\Exception $e) {
            return redirect()->route('sales-targets.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
