<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $query = Team::query();
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }
        $teams = $query->orderBy('id', 'desc')->paginate(10);
        return view('master.teams.index', compact('teams'));
    }

    public function create()
    {
        return view('master.teams.create');
    }

    public function store(StoreTeamRequest $request)
    {
        Team::create($request->validated());
        return redirect()->route('teams.index')->with('success', 'Team berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Team $team)
    {
        return view('master.teams.edit', compact('team'));
    }

    public function update(UpdateTeamRequest $request, Team $team)
    {
        $team->update($request->validated());
        return redirect()->route('teams.index')->with('success', 'Team berhasil diupdate.');
    }

    public function destroy(Team $team)
    {
        $team->delete();
        return redirect()->route('teams.index')->with('success', 'Team berhasil dihapus.');
    }
}
