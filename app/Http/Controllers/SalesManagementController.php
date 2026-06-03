<?php

namespace App\Http\Controllers;

use App\Models\SalesTarget;
use App\Models\SalesRealization;
use Illuminate\Http\Request;

class SalesManagementController extends Controller
{
    public function index(Request $request)
    {
        // TARGET QUERY
        $targetQuery = SalesTarget::with(['salesMember', 'entity']);
        if ($request->has('target_year') && $request->target_year) {
            $targetQuery->where('year', $request->target_year);
        }
        if ($request->has('target_month') && $request->target_month) {
            $targetQuery->where('month', $request->target_month);
        }
        $sales_targets = $targetQuery->orderBy('year', 'desc')->orderBy('month', 'desc')->paginate(10, ['*'], 'target_page');

        // REALISASI QUERY
        $realisasiQuery = SalesRealization::with(['salesMember', 'entity']);
        if ($request->has('realisasi_year') && $request->realisasi_year) {
            $realisasiQuery->where('year', $request->realisasi_year);
        }
        if ($request->has('realisasi_month') && $request->realisasi_month) {
            $realisasiQuery->where('month', $request->realisasi_month);
        }
        $sales_realizations = $realisasiQuery->orderBy('year', 'desc')->orderBy('month', 'desc')->paginate(10, ['*'], 'realisasi_page');

        return view('sales.management.index', compact('sales_targets', 'sales_realizations'));
    }
}
