<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalesActivityController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'per_page' => 'nullable|integer',
        ]);

        $query = \App\Models\SalesActivity::query();

        // Search filter
        if (!empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function($q) use ($search) {
                $q->where('sales_name', 'like', "%{$search}%")
                  ->orWhere('destination_name', 'like', "%{$search}%")
                  ->orWhere('activity_type', 'like', "%{$search}%")
                  ->orWhere('destination_type', 'like', "%{$search}%")
                  ->orWhere('team_involved', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if (!empty($validated['start_date'])) {
            $query->where('date', '>=', $validated['start_date']);
        }
        if (!empty($validated['end_date'])) {
            $query->where('date', '<=', $validated['end_date']);
        }

        // Pagination
        $perPage = $validated['per_page'] ?? 10;
        $activities = $query->orderBy('date', 'desc')
                            ->orderBy('start_time', 'desc')
                            ->paginate($perPage);

        return response()->json($activities);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'sales_name' => 'required|string',
            'destination_type' => 'nullable|string',
            'destination_name' => 'nullable|string',
            'team_involved' => 'nullable|string',
            'activity_type' => 'nullable|string',
            'activities' => 'nullable|string',
            'report' => 'nullable|string',
        ]);

        $activity = \App\Models\SalesActivity::create($validated);
        return response()->json(['message' => 'Activity created successfully', 'data' => $activity], 201);
    }

    public function show($id)
    {
        $activity = \App\Models\SalesActivity::findOrFail($id);
        return response()->json($activity);
    }

    public function update(Request $request, $id)
    {
        $activity = \App\Models\SalesActivity::findOrFail($id);
        
        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'sales_name' => 'required|string',
            'destination_type' => 'nullable|string',
            'destination_name' => 'nullable|string',
            'team_involved' => 'nullable|string',
            'activity_type' => 'nullable|string',
            'activities' => 'nullable|string',
            'report' => 'nullable|string',
        ]);

        $activity->update($validated);
        return response()->json(['message' => 'Activity updated successfully', 'data' => $activity]);
    }

    public function destroy($id)
    {
        $activity = \App\Models\SalesActivity::findOrFail($id);
        $activity->delete();
        return response()->json(['message' => 'Activity deleted successfully']);
    }
}
