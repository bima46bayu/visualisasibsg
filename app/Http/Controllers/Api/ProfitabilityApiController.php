<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profitability;
use App\Models\Entity;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProfitabilityExport;
use App\Imports\ProfitabilityImport;

class ProfitabilityApiController extends Controller
{
    private function mapItemsToFrontend($profitability)
    {
        $items = $profitability->items;
        $profitability->pendapatan_items = $items->where('category', 'pendapatan')->values();
        $profitability->hpp_items = $items->where('category', 'hpp')->values();
        $profitability->biaya_marketing_items = $items->where('category', 'biaya_marketing')->values();
        $profitability->biaya_admin_items = $items->where('category', 'biaya_admin')->values();
        $profitability->biaya_non_ops_items = $items->where('category', 'biaya_non_ops')->values();
        $profitability->pendapatan_lain_items = $items->where('category', 'pendapatan_lain')->values();
        $profitability->biaya_lain_items = $items->where('category', 'biaya_lain')->values();
        $profitability->pajak_items = $items->where('category', 'pajak')->values();
        
        unset($profitability->items);
        return $profitability;
    }

    public function index(Request $request)
    {
        $query = Profitability::with(['entity', 'items']);
        
        if ($request->year) $query->where('year', $request->year);
        if ($request->month) $query->where('month', $request->month);
        
        $paginator = $query->orderBy('year', 'desc')->orderBy('month', 'desc')->paginate(10);
        
        $paginator->getCollection()->transform(function ($prof) {
            return $this->mapItemsToFrontend($prof);
        });

        return response()->json($paginator);
    }

    public function dashboard(Request $request)
    {
        $year = $request->year ?: date('Y');

        $query = Profitability::with('items')->where('year', $year);
        $profitabilities = $query->get();

        $totalPendapatan = $profitabilities->sum('pendapatan');
        $totalLabaKotor = $profitabilities->sum('laba_kotor');
        $totalLabaOperasi = $profitabilities->sum('laba_operasi');
        $totalLabaSebelumPajak = $profitabilities->sum('laba_sebelum_pajak');
        $totalLabaBersih = $profitabilities->sum('laba_bersih');

        $trendData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthData = $profitabilities->where('month', $i);
            $trendData[] = [
                'month' => $i,
                'pendapatan' => $monthData->sum('pendapatan'),
                'laba_kotor' => $monthData->sum('laba_kotor'),
                'laba_bersih' => $monthData->sum('laba_bersih'),
            ];
        }

        $totalHPP = 0;
        $totalMarketing = 0;
        $totalAdmin = 0;
        $totalNonOps = 0;
        $totalPajak = 0;

        foreach ($profitabilities as $p) {
            $totalHPP += $p->items->where('category', 'hpp')->sum('amount');
            $totalMarketing += $p->items->where('category', 'biaya_marketing')->sum('amount');
            $totalAdmin += $p->items->where('category', 'biaya_admin')->sum('amount');
            $totalNonOps += $p->items->where('category', 'biaya_non_ops')->sum('amount');
            $totalPajak += $p->items->where('category', 'pajak')->sum('amount');
        }

        $costAllocation = [
            ['name' => 'HPP (COGS)', 'value' => $totalHPP, 'color' => '#DCF26B'],
            ['name' => 'Marketing', 'value' => $totalMarketing, 'color' => '#C2EAD4'],
            ['name' => 'Admin', 'value' => $totalAdmin, 'color' => '#BFE0F2'],
            ['name' => 'Non-Ops & Lain', 'value' => $totalNonOps, 'color' => '#F4D9C2'],
            ['name' => 'Pajak', 'value' => $totalPajak, 'color' => '#FFB6C1'],
        ];

        $entityMargin = [];
        $entities = Entity::all();
        foreach ($entities as $entity) {
            $entityData = $profitabilities->where('entity_id', $entity->id);
            if ($entityData->isEmpty()) continue;
            
            $pendapatan = $entityData->sum('pendapatan');
            $labaKotor = $entityData->sum('laba_kotor');
            $labaBersih = $entityData->sum('laba_bersih');
            $hpp = 0;
            foreach ($entityData as $ed) {
                $hpp += $ed->items->where('category', 'hpp')->sum('amount');
            }
            
            $entityMargin[] = [
                'entity' => $entity->name,
                'revenue' => $pendapatan,
                'cogs' => $hpp,
                'gross_margin' => $pendapatan > 0 ? round(($labaKotor / $pendapatan) * 100, 2) : 0,
                'net_margin' => $pendapatan > 0 ? round(($labaBersih / $pendapatan) * 100, 2) : 0,
            ];
        }

        usort($entityMargin, fn($a, $b) => $b['revenue'] <=> $a['revenue']);

        return response()->json([
            'summary' => [
                'pendapatan' => $totalPendapatan,
                'laba_kotor' => $totalLabaKotor,
                'laba_operasi' => $totalLabaOperasi,
                'laba_sebelum_pajak' => $totalLabaSebelumPajak,
                'laba_bersih' => $totalLabaBersih,
            ],
            'trend' => $trendData,
            'cost_allocation' => array_values(array_filter($costAllocation, fn($c) => $c['value'] > 0)),
            'entity_margin' => $entityMargin,
            'year' => $year
        ]);
    }

    private function syncItems($profitability, $data)
    {
        $profitability->items()->delete();
        $categories = [
            'pendapatan_items' => 'pendapatan',
            'hpp_items' => 'hpp',
            'biaya_marketing_items' => 'biaya_marketing',
            'biaya_admin_items' => 'biaya_admin',
            'biaya_non_ops_items' => 'biaya_non_ops',
            'pendapatan_lain_items' => 'pendapatan_lain',
            'biaya_lain_items' => 'biaya_lain',
            'pajak_items' => 'pajak'
        ];

        $insertData = [];
        foreach ($categories as $key => $category) {
            if (!empty($data[$key]) && is_array($data[$key])) {
                foreach ($data[$key] as $item) {
                    if (!isset($item['description']) && !isset($item['amount'])) continue;
                    $insertData[] = [
                        'category' => $category,
                        'description' => $item['description'] ?? '',
                        'amount' => $item['amount'] ?? 0,
                    ];
                }
            }
        }

        if (!empty($insertData)) {
            $profitability->items()->createMany($insertData);
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'drafts' => 'required|array',
            'drafts.*.year' => 'required|integer',
            'drafts.*.month' => 'required|integer',
            'drafts.*.entity_id' => 'required|string',
            'drafts.*.pendapatan_items' => 'nullable|array',
            'drafts.*.hpp_items' => 'nullable|array',
            'drafts.*.biaya_marketing_items' => 'nullable|array',
            'drafts.*.biaya_admin_items' => 'nullable|array',
            'drafts.*.biaya_non_ops_items' => 'nullable|array',
            'drafts.*.pendapatan_lain_items' => 'nullable|array',
            'drafts.*.biaya_lain_items' => 'nullable|array',
            'drafts.*.pajak_items' => 'nullable|array',
            'drafts.*.pendapatan' => 'nullable|numeric',
            'drafts.*.laba_kotor' => 'nullable|numeric',
            'drafts.*.total_biaya_overhead' => 'nullable|numeric',
            'drafts.*.laba_operasi' => 'nullable|numeric',
            'drafts.*.laba_sebelum_pajak' => 'nullable|numeric',
            'drafts.*.laba_bersih' => 'nullable|numeric',
        ]);

        foreach ($data['drafts'] as $d) {
            $payload = [
                'year' => $d['year'],
                'month' => $d['month'],
                'entity_id' => $d['entity_id'],
                'pendapatan' => $d['pendapatan'] ?? 0,
                'laba_kotor' => $d['laba_kotor'] ?? 0,
                'total_biaya_overhead' => $d['total_biaya_overhead'] ?? 0,
                'laba_operasi' => $d['laba_operasi'] ?? 0,
                'laba_sebelum_pajak' => $d['laba_sebelum_pajak'] ?? 0,
                'laba_bersih' => $d['laba_bersih'] ?? 0,
            ];

            if (isset($d['id']) && !empty($d['id'])) {
                $prof = Profitability::find($d['id']);
                if ($prof) {
                    $prof->update($payload);
                    $this->syncItems($prof, $d);
                    continue;
                }
            }

            $prof = Profitability::create($payload);
            $this->syncItems($prof, $d);
        }

        return response()->json(['message' => 'Profitability saved successfully']);
    }

    public function update(Request $request, $id)
    {
        $d = $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer',
            'entity_id' => 'required|string',
            'pendapatan_items' => 'nullable|array',
            'hpp_items' => 'nullable|array',
            'biaya_marketing_items' => 'nullable|array',
            'biaya_admin_items' => 'nullable|array',
            'biaya_non_ops_items' => 'nullable|array',
            'pendapatan_lain_items' => 'nullable|array',
            'biaya_lain_items' => 'nullable|array',
            'pajak_items' => 'nullable|array',
            'pendapatan' => 'nullable|numeric',
            'laba_kotor' => 'nullable|numeric',
            'total_biaya_overhead' => 'nullable|numeric',
            'laba_operasi' => 'nullable|numeric',
            'laba_sebelum_pajak' => 'nullable|numeric',
            'laba_bersih' => 'nullable|numeric',
        ]);

        $prof = Profitability::findOrFail($id);
        $prof->update([
            'year' => $d['year'],
            'month' => $d['month'],
            'entity_id' => $d['entity_id'],
            'pendapatan' => $d['pendapatan'] ?? 0,
            'laba_kotor' => $d['laba_kotor'] ?? 0,
            'total_biaya_overhead' => $d['total_biaya_overhead'] ?? 0,
            'laba_operasi' => $d['laba_operasi'] ?? 0,
            'laba_sebelum_pajak' => $d['laba_sebelum_pajak'] ?? 0,
            'laba_bersih' => $d['laba_bersih'] ?? 0,
        ]);

        $this->syncItems($prof, $d);

        return response()->json(['message' => 'Profitability updated successfully']);
    }

    public function destroy($id)
    {
        Profitability::findOrFail($id)->delete();
        return response()->json(['message' => 'Profitability deleted successfully']);
    }

    public function export(Request $request)
    {
        return Excel::download(new ProfitabilityExport(false), 'profitability_data.xlsx');
    }

    public function exportTemplate(Request $request)
    {
        return Excel::download(new ProfitabilityExport(true), 'profitability_template.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new ProfitabilityImport(), $request->file('file'));

        return response()->json(['message' => 'Data profitability berhasil diimpor']);
    }
}
