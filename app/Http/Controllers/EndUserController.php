<?php

namespace App\Http\Controllers;

use App\Models\EndUser;
use App\Http\Requests\StoreEndUserRequest;
use App\Http\Requests\UpdateEndUserRequest;
use Illuminate\Http\Request;

class EndUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EndUser::query();
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }
        $endUsers = $query->orderBy('id', 'desc')->paginate(10);
        return view('master.end-users.index', compact('endUsers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.end-users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEndUserRequest $request)
    {
        EndUser::create($request->validated());
        return redirect()->route('end-users.index')->with('success', 'End User berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EndUser $endUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EndUser $endUser)
    {
        return view('master.end-users.edit', compact('endUser'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEndUserRequest $request, EndUser $endUser)
    {
        $endUser->update($request->validated());
        return redirect()->route('end-users.index')->with('success', 'End User berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EndUser $endUser)
    {
        $endUser->delete();
        return redirect()->route('end-users.index')->with('success', 'End User berhasil dihapus.');
    }
}
