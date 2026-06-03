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
        $salesMember = SalesMember::firstOrCreate(['name' => $request->sales_member_name]);
        $entity = Entity::firstOrCreate(['name' => $request->entity_name]);

        SalesTarget::updateOrCreate(
            [
                'year' => $request->year,
                'month' => $request->month,
                'sales_member_id' => $salesMember->id,
                'entity_id' => $entity->id
            ],
            ['target_amount' => $request->target_amount]
        );
        return redirect()->route('sales-management.index', ['tab' => 'target'])->with('success', 'Target berhasil ditambahkan.');
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
        $salesMember = SalesMember::firstOrCreate(['name' => $request->sales_member_name]);
        $entity = Entity::firstOrCreate(['name' => $request->entity_name]);

        $target->update([
            'year' => $request->year,
            'month' => $request->month,
            'sales_member_id' => $salesMember->id,
            'entity_id' => $entity->id,
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
            return redirect()->route('sales-management.index', ['tab' => 'target'])->with('error', 'Ada kesalahan pada baris excel. Pastikan data valid.');
        } catch (\Exception $e) {
            return redirect()->route('sales-management.index', ['tab' => 'target'])->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
