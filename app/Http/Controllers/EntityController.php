<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Http\Requests\StoreEntityRequest;
use App\Http\Requests\UpdateEntityRequest;
use Illuminate\Http\Request;

class EntityController extends Controller
{
    public function index(Request $request)
    {
        $query = Entity::query();
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }
        $entities = $query->orderBy('id', 'desc')->paginate(10);
        return view('master.entities.index', compact('entities'));
    }

    public function create()
    {
        return view('master.entities.create');
    }

    public function store(StoreEntityRequest $request)
    {
        Entity::create($request->validated());
        return redirect()->route('entities.index')->with('success', 'Entity berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Entity $entity)
    {
        return view('master.entities.edit', compact('entity'));
    }

    public function update(UpdateEntityRequest $request, Entity $entity)
    {
        $entity->update($request->validated());
        return redirect()->route('entities.index')->with('success', 'Entity berhasil diupdate.');
    }

    public function destroy(Entity $entity)
    {
        $entity->delete();
        return redirect()->route('entities.index')->with('success', 'Entity berhasil dihapus.');
    }
}
