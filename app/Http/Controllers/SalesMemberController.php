<?php

namespace App\Http\Controllers;

use App\Models\SalesMember;
use App\Models\Team;
use App\Http\Requests\StoreSalesMemberRequest;
use App\Http\Requests\UpdateSalesMemberRequest;
use Illuminate\Http\Request;

class SalesMemberController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesMember::with('team');
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }
        $sales_members = $query->orderBy('id', 'desc')->paginate(10);
        return view('master.sales-members.index', compact('sales_members'));
    }

    public function create()
    {
        $teams = Team::all();
        return view('master.sales-members.create', compact('teams'));
    }

    public function store(StoreSalesMemberRequest $request)
    {
        SalesMember::create($request->validated());
        return redirect()->route('sales-members.index')->with('success', 'Sales Member berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(SalesMember $sales_member)
    {
        $teams = Team::all();
        return view('master.sales-members.edit', compact('sales_member', 'teams'));
    }

    public function update(UpdateSalesMemberRequest $request, SalesMember $sales_member)
    {
        $sales_member->update($request->validated());
        return redirect()->route('sales-members.index')->with('success', 'Sales Member berhasil diupdate.');
    }

    public function destroy(SalesMember $sales_member)
    {
        $sales_member->delete();
        return redirect()->route('sales-members.index')->with('success', 'Sales Member berhasil dihapus.');
    }
}
